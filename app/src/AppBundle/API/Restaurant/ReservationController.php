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

        $json = array();
        foreach($reservations as $reservation){
            $jsonContents=array();
            $contents = $elasticaManager->getRepository('AppBundle:ReservationContent')->findByReservation($reservation);
            foreach($contents as $content) {
                $jsonContents[]=array(
                    "id" => $content->getContent()->getId(),
                    "name" => $content->getContent()->getName(),
                    "quantity" => $content->getQuantity(),
                    "totalPrice" => $content->getTotalPrice()
                );
            }
            $json[] = array(
                "id" => $reservation->getId(),
                "date" => $reservation->getDate(),
                "nbParticipants" => $reservation->getNbParticipants(),
                "total" => $reservation->getTotal(),
                "state" => $reservation->getState(),
                "user" => array(
                    "id" => $reservation->getUser()->getId(),
                    "lastname" => $reservation->getUser()->getLastName(),
                    "firstname" => $reservation->getUser()->getFirstName(),
                    "phoneNumber" => $reservation->getUser()->getPhoneNumber(),
                ),
                "restaurant" => array(
                    "id" => $reservation->getRestaurant()->getId(),
                    "name" => $reservation->getRestaurant()->getName(),
                    "picture" => $reservation->getRestaurant()->getPicture(),
                ),
                "content" => $jsonContents,
            );
        }

        return $this->helper->success($json, 200);
    }

    /**
     * @REST\Get("/restaurants/{id}/availabilities", name="api_list_availabilites")
     *
     * @QueryParam(name="date_from")
     * @QueryParam(name="date_to")
     * @QueryParam(name="nb_participants")
     */
    public function getAvailabilites(Request $request, ParamFetcher $paramFetcher) {
        $params = $paramFetcher->all();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $dateFrom = new \DateTime($params['date_from']);
        $dateTo = new \DateTime($params['date_to']);
        $nbParticipants = $params['nb_participants'];

        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));

        $elasticaManager = $this->container->get('fos_elastica.manager');

        $jsonSchedule = json_decode($restaurant->getSchedule(),true);
        while($dateFrom <= $dateTo) {
            foreach ($jsonSchedule as $schedule) {
                $currentDate = clone $dateFrom;
                $stringCurrentDay = $currentDate->format('l');
                $stringDay = $schedule["name"];
                $verif = true;
                $available = false;

                if ($stringCurrentDay == "Sunday" && $stringDay == "Dim") {
                    $arrayHours = $schedule["timeSteps"];
                } elseif ($stringCurrentDay == "Monday" && $stringDay == "Lun") {
                    $arrayHours = $schedule["timeSteps"];
                } elseif ($stringCurrentDay == "Tuesday" && $stringDay == "Mar") {
                    $arrayHours = $schedule["timeSteps"];
                } elseif ($stringCurrentDay == "Wednesday" && $stringDay == "Mer") {
                    $arrayHours = $schedule["timeSteps"];
                } elseif ($stringCurrentDay == "Thursday" && $stringDay == "Jeu") {
                    $arrayHours = $schedule["timeSteps"];
                } elseif ($stringCurrentDay == "Friday" && $stringDay == "Ven") {
                    $arrayHours = $schedule["timeSteps"];
                } elseif ($stringCurrentDay == "Saturday" && $stringDay == "Sam") {
                    $arrayHours = $schedule["timeSteps"];
                }
                else
                    $verif=false;

                if($verif) {
                    foreach ($arrayHours as $hour) {
                        $dateOrigin = clone $dateFrom;
                        $dayOrigin = $dateOrigin->format('Y-m-d');
                        $dayFromString = $dateFrom->format('Y-m-d');
                        $dateOriginCompareToTime = $dayOrigin . "T" . $hour . ":00Z";
                        $dateFromCompare = new \DateTime($dateOriginCompareToTime);
                        $dateToCompare = clone $dateFromCompare;
                        $dateToCompare->modify("+29 minutes");


                        $reservations = $elasticaManager->getRepository('AppBundle:Reservation')->findByRestaurant($restaurant, $dateFromCompare, $dateToCompare);
                        $seats = $restaurant->getSeats();
                        if (isset($reservations)) {
                            foreach ($reservations as $reservation) {
                                $seats = $seats - $reservation->getNbParticipants();
                            }
                        }
                        if ($seats >= $nbParticipants) {
                            $available = true;
                        }
                    }
                }
                if($available){
                    $json[] = $dateOrigin;
                }
            }
            $dateFrom->modify("+1 day");
        }
        if(!isset($json))
            return $this->helper->error("Aucune disponibilité");

        return $this->helper->success($json, 200);
    }

    /**
     * @REST\Get("/restaurants/{id}/daily_availabilities", name="api_list_daily_availabilites")
     *
     * @QueryParam(name="date")
     * @QueryParam(name="nb_participants")
     */
    public function getDailyAvailabilites(Request $request, ParamFetcher $paramFetcher) {
        $params = $paramFetcher->all();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $dateFrom = new \DateTime($params['date']);
        $nbParticipants = $params['nb_participants'];

        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));

        $elasticaManager = $this->container->get('fos_elastica.manager');

        $jsonSchedule = json_decode($restaurant->getSchedule(),true);

        foreach ($jsonSchedule as $schedule) {
            $currentDate = clone $dateFrom;
            $stringCurrentDay = $currentDate->format('l');
            $stringDay = $schedule["name"];
            $verif = true;


            if ($stringCurrentDay == "Sunday" && $stringDay == "Dim") {
                $arrayHours = $schedule["timeSteps"];
            } elseif ($stringCurrentDay == "Monday" && $stringDay == "Lun") {
                $arrayHours = $schedule["timeSteps"];
            } elseif ($stringCurrentDay == "Tuesday" && $stringDay == "Mar") {
                $arrayHours = $schedule["timeSteps"];
            } elseif ($stringCurrentDay == "Wednesday" && $stringDay == "Mer") {
                $arrayHours = $schedule["timeSteps"];
            } elseif ($stringCurrentDay == "Thursday" && $stringDay == "Jeu") {
                $arrayHours = $schedule["timeSteps"];
            } elseif ($stringCurrentDay == "Friday" && $stringDay == "Ven") {
                $arrayHours = $schedule["timeSteps"];
            } elseif ($stringCurrentDay == "Saturday" && $stringDay == "Sam") {
                $arrayHours = $schedule["timeSteps"];
            }
            else
                $verif=false;

            if($verif) {
                foreach ($arrayHours as $hour) {
                    $dateOrigin = clone $dateFrom;
                    $dayOrigin = $dateOrigin->format('Y-m-d');
                    $dayFromString = $dateFrom->format('Y-m-d');
                    $dateOriginCompareToTime = $dayOrigin . "T" . $hour . ":00Z";
                    $dateFromCompareToTime = $dayFromString . "T" ."00:00:00Z";
                    $dateFromCompare = new \DateTime($dateOriginCompareToTime);
                    $dateToCompare = clone $dateFromCompare;
                    $dateToCompare->modify("+29 minutes");


                    $reservations = $elasticaManager->getRepository('AppBundle:Reservation')->findByRestaurant($restaurant, $dateFromCompare, $dateToCompare);
                    $seats = $restaurant->getSeats();
                    if (isset($reservations)) {
                        foreach ($reservations as $reservation) {
                            $seats = $seats - $reservation->getNbParticipants();
                        }
                    }
                    if ($seats >= $nbParticipants) {
                        $json["availabilities"][] = array($hour => $seats);
                    }
                }
            }
        }
        if(!isset($json))
            return $this->helper->error("Aucune disponibilité");

        return $this->helper->success($json, 200);
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
        $dateTo = new \DateTime($params['date']);

        $elasticaManager = $this->container->get('fos_elastica.manager');


        $dateTo->modify("+29 minutes");
        $reservations = $elasticaManager->getRepository('AppBundle:Reservation')->findByRestaurant($restaurant, $dateFrom, $dateTo);
        $seats = $restaurant->getSeats();
        if(isset($reservations)){
            foreach ($reservations as $reservation){
                if($reservation->getState() != Reservation::STATE_CANCELED)
                    $seats = $seats-$reservation->getNbParticipants();
            }
        }
        if($seats>=$nbParticipants) {
            $availability=array("availability" => $seats);
        }
        else{
            $availability=array("availability" => false);
        }

        return $this->helper->success($availability, 200);
    }

    /**
     * @REST\Get("/restaurants/{id}/reservations/{idReservation}", name="api_show_restaurant_reservation")
     */
    public function getRestaurantReservation(Request $request) {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

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

        $reservation = $elasticaManager->getRepository('AppBundle:Reservation')->findById($request->get('idReservation'));

        if (!$reservation) {
            return $this->helper->elementNotFound('Reservation');
        }
        if($reservation->getRestaurant()!= $restaurant){
            return $this->helper->error('Ce n\'est pas une réservation concernant votre restaurant');
        }

        $jsonContents=array();
        $contents = $elasticaManager->getRepository('AppBundle:ReservationContent')->findByReservation($reservation);
        foreach($contents as $content) {
            $jsonContents[]=array(
                "id" => $content->getContent()->getId(),
                "name" => $content->getContent()->getName(),
                "quantity" => $content->getQuantity(),
                "totalPrice" => $content->getTotalPrice()
            );
        }
        $reservation = array(
            "id" => $reservation->getId(),
            "date" => $reservation->getDate(),
            "nbParticipants" => $reservation->getNbParticipants(),
            "total" => $reservation->getTotal(),
            "state" => $reservation->getState(),
            "user" => array(
                "id" => $reservation->getUser()->getId(),
                "lastname" => $reservation->getUser()->getLastName(),
                "firstname" => $reservation->getUser()->getFirstName(),
                "phoneNumber" => $reservation->getUser()->getPhoneNumber(),
            ),
            "restaurant" => array(
                "id" => $reservation->getRestaurant()->getId(),
                "name" => $reservation->getRestaurant()->getName(),
                "picture" => $reservation->getRestaurant()->getPicture(),
            ),
            "content" => $jsonContents,
        );

        return $this->helper->success($reservation, 200);
    }
}
