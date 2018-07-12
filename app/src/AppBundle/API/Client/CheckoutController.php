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
     *
     * @return View
     */
    public function createReservation(Request $request, ParamFetcher $paramFetcher)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $params = $paramFetcher->all();

        if($user->getType()!= User::TYPE_CLIENT){
            return $this->helper->error('Seul un client peut effectuer une réservation');
        }

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$params['date']) {
            return $this->helper->error('date', true);
        }

        if (!$params['seats']) {
            return $this->helper->error('seats', true);
        } elseif (!is_array($params['seats'])) {
            return $this->helper->error('param \'meals_id\' must be an array');
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
                        return $this->helper->error('Le plat '.$mealContent->getId().' ne fait pas partie de ce restaurant');
                    }
                }


            }
        }



        if(!$checkMeal){
            return $this->helper->error('Vous n\'avez sélectionné aucun plat');
        }
        $dateNow=new \DateTime();
        $dateFrom=new \DateTime($params["date"]);
        $dateTo=new \DateTime($params["date"]);
        $dateTo->modify("+29 minutes");

        if($dateFrom < $dateNow){
            return $this->helper->error("Vous ne pouvez pas réserver à une date antérieure");
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
            return $this->helper->error($seats >= 1 ? "Il ne reste plus que ".$seats." place(s) de disponible(s)." : "Il ne reste plus de place.");
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
            $seatPerson->setName($person["name"]);
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


        $mailer = $this->container->get('mailer');
        $message = (new \Swift_Message('Réservation effectuée N°'.$reservation->getId()))
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    '@App/mails/submit_reservation.html.twig',
                    array(
                        'user' => $user,
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
     * @REST\Get("/restaurants/{id}/reservations/{idReservation}/confirm", name="api_confirm_reservation")
     *
     * @return View
     */
    public function confirmReservation(Request $request)
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
            return $this->helper->elementNotFound('Reservation');

        }
        if($reservation->getState() == Reservation::STATE_PAID){
            return $this->helper->error('Cette commande a déjà été validée');
        }
        if($reservation->getState() == Reservation::STATE_CANCELED){
            return $this->helper->error('Cette commande a été annulée');
        }

        $reservation->setState(Reservation::STATE_PAID);
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