<?php

namespace AppBundle\API\Client ;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Reservation;
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
     * @REST\Post("/restaurant/{id}/reservation", name="api_create_reservation")
     * @REST\RequestParam(name="total")
     * @REST\RequestParam(name="nbParticipants")
     * @REST\RequestParam(name="date")
     * @REST\RequestParam(name="meals_id")
     *
     * @return View
     */
    public function createReservation(Request $request, ParamFetcher $paramFetcher)
    {
        //$user = $this->container->get('security.token_storage')->getToken()->getUser();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find(12);
        $params = $paramFetcher->all();

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
            return $this->helper->error('param \'id\' must be a float number');
        }

        if (!$params['date']) {
            return $this->helper->error('date', true);
        } elseif (!preg_match('/^(\d{4}-{1}\d{2}-{1}\d{2}\s\d{2}:{1}\d{2})$/', $params['date'])) {
            return $this->helper->error('param \'date\' must be a date time');
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
        
        $meals = $elasticaManager->getRepository('AppBundle:Content')->findByIds($params['meals_id']);
        $reservation = new Reservation($user, $restaurant);
        $reservation->setRestaurant($restaurant);
        $reservation->setUser($user);
        $reservation->setContents($meals);
        unset($params['meals_id']);

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }

        $em = $this->getEntityManager();
        $em->persist($reservation);
        $em->flush();

        return $this->helper->success($reservation, 200);
    }
}