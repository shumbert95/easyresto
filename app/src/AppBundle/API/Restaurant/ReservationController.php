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
     * @REST\Get("/restaurants/{id}/reservations", name="api_list_reservations")
     *
     * @QueryParam(name="date_from")
     * @QueryParam(name="date_to")
     */
    public function getReservations(Request $request, ParamFetcher $paramFetcher) {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $params = $paramFetcher->all();

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

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $dateFrom = new \DateTime($params['date_from']);
        $dateTo = new \DateTime($params['date_to']);


        $reservations = $elasticaManager->getRepository('AppBundle:Reservation')->findByRestaurant($restaurant, $dateFrom, $dateTo);
        return $this->helper->success($reservations, 200);
    }

    /**
     * @REST\Get("/restaurants/{id}/availabilities", name="api_list_availabilites")
     *
     * @QueryParam(name="date_from")
     * @QueryParam(name="date_to")
     */
    public function getAvailabilites(Request $request, ParamFetcher $paramFetcher) {
        $params = $paramFetcher->all();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }



        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));


        $dateFrom = new \DateTime($params['date_from']);
        $dateTo = new \DateTime($params['date_to']);
        $dateToCompare = new \DateTime(($params['date_from']));

        $elasticaManager = $this->container->get('fos_elastica.manager');

        $availabilities=array();

        while($dateFrom<$dateTo){
            $hour=$dateFrom->format('H:i');
            $dateToCompare->modify("+29 minutes");
            $reservations = $elasticaManager->getRepository('AppBundle:Reservation')->findByRestaurant($restaurant, $dateFrom, $dateToCompare);
            $seats = $restaurant->getSeats();
            if(isset($reservations)){
                foreach ($reservations as $reservation){
                    $seats = $seats-$reservation->getNbParticipants();
                }
            }
            if($seats>0) {
                $availabilities["availabilites"][$hour] = array("available_seats" => $seats);
            }
            else{
                $availabilities["availabilites"][$hour] = array("available_seats" => "Indisponible");
            }

            $dateToCompare->modify("+1 minute");
            $dateFrom->modify("+30 minutes");
        }

        return $this->helper->success($availabilities, 200);
    }

    /**
     * @REST\Get("/restaurants/{id}/check_availability", name="api_check_availability")
     *
     * @QueryParam(name="date")
     * @QueryParam(name="nb_participants")
     */
    public function checkAvailability(Request $request, ParamFetcher $paramFetcher) {
        $params = $paramFetcher->all();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }



        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));

        $nbParticipants = $params['nb_participants'];

        $dateFrom = new \DateTime($params['date']);
        $dateTo = new \DateTime(($params['date']));

        $elasticaManager = $this->container->get('fos_elastica.manager');


        $dateTo->modify("+29 minutes");
        $reservations = $elasticaManager->getRepository('AppBundle:Reservation')->findByRestaurant($restaurant, $dateFrom, $dateTo);
        $seats = $restaurant->getSeats();
        if(isset($reservations)){
            foreach ($reservations as $reservation){
                $seats = $seats-$reservation->getNbParticipants();
            }
        }
        if($seats>=$nbParticipants) {
            $availability=array("availability" => true);
        }
        else{
            $availability=array("availability" => false);
        }

        $dateTo->modify("+1 minute");
        $dateFrom->modify("+30 minutes");

        return $this->helper->success($availability, 200);
    }
}
