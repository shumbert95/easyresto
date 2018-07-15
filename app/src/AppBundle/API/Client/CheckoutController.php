<?php

namespace AppBundle\API\Client ;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Content;
use AppBundle\Entity\Reservation;
use AppBundle\Entity\ReservationContent;
use AppBundle\Entity\ReservationSeat;
use AppBundle\Entity\User;
use AppBundle\Form\ReservationType;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as REST;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Validator\Constraints\DateTime;


class CheckoutController extends ApiBaseController
{
    /**
     * @param Request $request
     *
     * @REST\Post("/restaurants/{id}/reservations", name="api_create_reservation")
     * @REST\RequestParam(name="date")
     * @REST\RequestParam(name="seats")
     * @REST\RequestParam(name="timeStep")
     * @REST\RequestParam(name="nbParticipants",nullable=true)
     *
     * @return View
     */
    public function createReservation(Request $request, ParamFetcher $paramFetcher)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $params = $paramFetcher->all();

        if($user->getType()!= User::TYPE_CLIENT){
            return $this->helper->warning('Seul un client peut effectuer une réservation',403);
        }

        if (!$request->get('id')) {
            return $this->helper->warning('Il manque le paramètre Id', 400);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->warning('Le paramètre Id doit être un integer',400);
        }

        if (!$params['date']) {
            return $this->helper->warning('Il manque le paramètre date', 400);
        }

        if (!$params['seats']) {
            return $this->helper->warning('Il manque le paramètre seats', 400);
        } elseif (!is_array($params['seats'])) {
            return $this->helper->warning('Le paramètre seats doit être un array', 400);
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));

        if (!$restaurant) {
            return $this->helper->elementNotFound('Restaurant');
        }
        $checkMeal=false;

        $arraySeats = $params['seats'];
        unset($params['seats']);
        unset($params['currentSeatIndex']);
        unset($params['restaurantId']);
        unset($params['userId']);

        $nbParticipants = 0;
        foreach($arraySeats as $person){
            $nbParticipants++;
            foreach($person["meals"] as $meal){
                $mealContent = $elasticaManager->getRepository('AppBundle:Content')->findById($meal["id"]);
                if($mealContent && $mealContent->getType()==Content::TYPE_MEAL) {
                    $checkMeal = true;
                    if($mealContent->getRestaurant()!=$restaurant){
                        return $this->helper->error('Le plat '.$mealContent->getId().' ne fait pas partie de ce restaurant',403);
                    }
                }


            }
        }


        //pour mobile
        $verifMobile=false;
        if(isset($params['nbParticipants'])) {
            $verifMobile=true;
            $nbParticipants = $params['nbParticipants'];
        }
        unset($params['nbParticipants']);


        if(!$checkMeal){
            return $this->helper->warning('Vous n\'avez sélectionné aucun plat',400);
        }
        $dateNow=new \DateTime();
        $dateFrom=new \DateTime($params["date"]);
        $dateTo=new \DateTime($params["date"]);
        $dateTo->modify("+29 minutes");

        if($dateFrom < $dateNow){
            return $this->helper->warning("Vous ne pouvez pas réserver à une date antérieure",400);
        }


        $reservations = $elasticaManager->getRepository('AppBundle:Reservation')->findByRestaurant($restaurant, $dateFrom, $dateTo);
        $seats = $restaurant->getSeats();

        if(isset($reservations)){
            foreach ($reservations as $reservation){
                if($reservation->getState() != Reservation::STATE_CANCELED)
                    $seats = $seats-$reservation->getNbParticipants();
            }
        }

        if($nbParticipants > $seats) {
            return $this->helper->warning($seats >= 1 ? "Il ne reste plus que ".$seats." place(s) de disponible(s)." : "Il ne reste plus de place.",400);
        }

        $reservation = new Reservation($user, $restaurant);
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->submit($params);
        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }
        $em = $this->getEntityManager();
        $reservation->setState(Reservation::STATE_PENDING);
        $reservation->setNbParticipants($nbParticipants);

        $em->persist($reservation);
        $em->flush();

        $total = 0;
        foreach($arraySeats as $person){
            $seatPerson = new ReservationSeat();
            if(!$verifMobile)
                $seatPerson->setName($person["name"]);
            else
                $seatPerson->setName("Mobile");
            $em->persist($seatPerson);
            $em->flush();
            foreach($person["meals"] as $meal) {
                $idMeal=$meal["id"];
                $meal = $elasticaManager->getRepository('AppBundle:Content')->findById($idMeal);

                if ($meal && $meal->getType() == Content::TYPE_MEAL) {
                    $reservationContent = $this->getReservationContentRepository()->findOneBy(array("content" => $meal, "seat" => $seatPerson));
                    if (!is_object($reservationContent)) {
                        $reservationContent = new ReservationContent();
                        $reservationContent->setContent($meal);
                        $reservationContent->setReservation($reservation);
                        $reservationContent->setTotalPrice($meal->getPrice());
                        $reservationContent->setQuantity(1);
                        $reservationContent->setSeat($seatPerson);
                    } else {
                        $reservationContent->setQuantity($reservationContent->getQuantity() + 1);
                        $reservationContent->setTotalPrice($reservationContent->getTotalPrice() + $meal->getPrice());

                    }
                    $total = $total + $meal->getPrice();
                    if($meal->getIngredients()) {
                        foreach ($meal->getIngredients() as $ingredient) {
                            $ingredient->setStock($ingredient->getStock() - 1);
                            $em->persist($ingredient);
                            $em->flush();
                        }
                    }
                    $em->persist($reservationContent);
                    $em->flush();
                }

            }
        }
        $reservation->setTotal($total);
        $em->persist($reservation);
        $em->flush();


        return $this->helper->success($reservation, 200);
    }

    /**
     * @param Request $request
     *
     * @REST\Post("/restaurants/{id}/reservations/{idReservation}/paypalconfirm/{idPaypal}", name="api_confirm_reservation_paypal")
     *
     * @return View
     */
    public function confirmReservationPaypal(Request $request)
    {
        $paypalResponse = $request->request->all();
        $paypalId = $request->get('idPaypal');

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idReservation')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('idReservation'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if($paypalResponse['id'] != $paypalId || $paypalResponse['state'] != 'approved'){
            return $this->helper->error('Paiement refusé');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');

        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));
        if (!$restaurant) {
            return $this->helper->elementNotFound('Restaurant');
        }


        $reservation = $elasticaManager->getRepository('AppBundle:Reservation')->findById($request->get('idReservation'));
        if(!$reservation){
            return $this->helper->elementNotFound('Reservation');

        }
        if($reservation->getState() == Reservation::STATE_PAID){
            return $this->helper->error('Cette commande a déjà été validée');
        }
        if($reservation->getState() == Reservation::STATE_CANCELED){
            return $this->helper->error('Cette commande a été annulée');
        }

        $reservation->setState(Reservation::STATE_PAID);
        $reservation->setPaymentMethod("Paypal");
        $reservation->setPaymentId($paypalId);
        $em = $this->getEntityManager();
        $em->persist($reservation);
        $em->flush();

        $mailer = $this->container->get('mailer');
        $message = (new \Swift_Message('Réservation N°'.$reservation->getId()." confirmée"))
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($reservation->getUser()->getEmail())
            ->setBody(
                $this->renderView(
                    '@App/mails/confirmation_commande.html.twig',
                    array(
                        'user' => $reservation->getUser(),
                        'reservation' => $reservation,
                        'restaurant' => $restaurant
                    )
                ),
                'text/html'
            );
        $mailer->send($message);

        return $this->helper->success($reservation, 200);
    }

    /**
     * @param Request $request
     *
     * @REST\Get("/restaurants/{id}/reservations/{idReservation}/cancel", name="api_cancel_reservation")
     *
     * @return View
     */
    public function cancelReservation(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idReservation')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('idReservation'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');

        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));
        if (!$restaurant) {
            return $this->helper->elementNotFound('Restaurant');
        }

        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $reservation = $elasticaManager->getRepository('AppBundle:Reservation')->findById($request->get('idReservation'));
        if(!$reservation){
            return $this->helper->elementNotFound('Reservation',true);

        }
        if($reservation->getState() == Reservation::STATE_CANCELED){
            return $this->helper->error('Cette commande a déjà été annulée');
        }
        if($reservation->getState() == Reservation::STATE_PAID){
            return $this->helper->error('Cette commande a été déjà été validée');
        }

        $reservation->setState(Reservation::STATE_CANCELED);
        $reservation->setDateCanceled(New \DateTime());
        $em = $this->getEntityManager();
        $em->persist($reservation);
        $em->flush();

        $mailer = $this->container->get('mailer');
        $message = (new \Swift_Message('Réservation N°'.$reservation->getId()." annulée"))
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($reservation->getUser()->getEmail())
            ->setBody(
                $this->renderView(
                    '@App/mails/canceled_reservation.html.twig',
                    array(
                        'user' => $reservation->getUser(),
                        'reservation' => $reservation,
                        'restaurant' => $restaurant
                    )
                ),
                'text/html'
            );
        $mailer->send($message);

        return $this->helper->success($reservation, 200);
    }


}