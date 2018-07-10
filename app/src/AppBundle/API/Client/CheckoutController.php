<?php

namespace AppBundle\API\Client ;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Content;
use AppBundle\Entity\Reservation;
use AppBundle\Entity\ReservationContent;
use AppBundle\Entity\User;
use AppBundle\Form\ReservationType;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as REST;


class CheckoutController extends ApiBaseController
{
    /**
     * @param Request $request
     *
     * @REST\Post("/restaurants/{id}/reservation", name="api_create_reservation")
     * @REST\RequestParam(name="total")
     * @REST\RequestParam(name="nbParticipants")
     * @REST\RequestParam(name="date")
     * @REST\RequestParam(name="meals_id")
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

        if (!$params['nbParticipants']) {
            return $this->helper->error('nbParticipants', true);
        } elseif (!preg_match('/\d/', $params['nbParticipants'])) {
            return $this->helper->error('param \'nbParticipants\' must be an integer');
        }

        if (!$params['total']) {
            return $this->helper->error('total', true);
        } elseif (!preg_match('/^(\d+.{1}\d+)$/', $params['total'])) {
            return $this->helper->error('param \'total\' must be a float number');
        }

        if (!$params['date']) {
            return $this->helper->error('date', true);
        }

        if (!$params['meals_id']) {
            return $this->helper->error('meals_id', true);
        } elseif (!is_array($params['meals_id'])) {
            return $this->helper->error('param \'meals_id\' must be an array');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));
        if (!$restaurant) {
            return $this->helper->elementNotFound('Restaurant');
        }
        $checkMeal=false;

        $arrayMeals = $params['meals_id'];
        $meals = $elasticaManager->getRepository('AppBundle:Content')->findByIds($arrayMeals,$restaurant,true);
        foreach($meals as $meal){
            if($meal->getType()==Content::TYPE_MEAL)
                $checkMeal=true;
        }
        unset($params['meals_id']);

        if(!$checkMeal){
            return $this->helper->error('Vous n\'avez sélectionné aucun plat');
        }

        $reservation = new Reservation($user, $restaurant);
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->submit($params);
        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }
        $em = $this->getEntityManager();
        $em->persist($reservation);
        $em->flush();

        $total = 0;
        foreach($arrayMeals as $idMeal){
            $meal = $elasticaManager->getRepository('AppBundle:Content')->findById($idMeal);

            if($meal->getType()==Content::TYPE_MEAL){
                $reservationContent = $this->getReservationContentRepository()->findOneBy(array("content" => $meal, "reservation" => $reservation));
                if(!is_object($reservationContent)) {
                    $reservationContent = new ReservationContent();
                    $reservationContent->setContent($meal);
                    $reservationContent->setReservation($reservation);
                    $reservationContent->setTotalPrice($meal->getPrice());
                    $reservationContent->setQuantity(1);
                }
                else{
                    $reservationContent->setQuantity($reservationContent->getQuantity()+1);
                    $reservationContent->setTotalPrice($reservationContent->getTotalPrice()+$meal->getPrice());

                }
                $total=$total + $meal->getPrice();
                $em->persist($reservationContent);
                $em->flush();
            }

        }
        $reservation->setTotal($total);
        $em->persist($reservation);
        $em->flush();

        return $this->helper->success($reservation, 200);
    }


}