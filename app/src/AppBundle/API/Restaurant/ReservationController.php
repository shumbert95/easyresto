<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\Validator\Constraints\Date;


class ReservationController extends ApiBaseController
{
    /**
     * @REST\Get("/restaurant/{id}/reservations", name="api_list_reservations")
     *
     * @QueryParam(name="date_from")
     * @QueryParam(name="date_to")
     */
    public function getReservations(Request $request, ParamFetcher $paramFetcher) {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $params = $paramFetcher->all();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }



        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        $restaurantUsers = $restaurant->getUsers();

        $dateFrom = new \DateTime($params['date_from']);
        $dateTo = new \DateTime($params['date_to']);


        $elasticaManager = $this->container->get('fos_elastica.manager');
        $reservations = $elasticaManager->getRepository('AppBundle:Reservation')->findByRestaurant($restaurant, $dateFrom, $dateTo);

        return $this->helper->success($reservations, 200);
    }
}
