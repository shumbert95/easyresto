<?php

namespace AppBundle\API\Client;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Client;
use AppBundle\Entity\Note;
use AppBundle\Entity\Reservation;
use AppBundle\Entity\Restaurant;
use AppBundle\Entity\User;
use AppBundle\Form\RegistrationClientType;
use AppBundle\Form\RegistrationRestorerType;
use AppBundle\Form\RestaurantType;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ClientController extends ApiBaseController
{


    /**
     * @REST\Get("/clients/{id}", name="api_detail_client")
     *
     */
    public function getClientById(Request $request)
    {
        $user = $this->getUserRepository()->find($request->get('id'));
        if($user->getType() != User::TYPE_CLIENT){
            return $this->helper->error('Cet utilisateur n\'est pas un client');
        }
        return $this->helper->success($user, 200);
    }


    /**
     * @REST\Get("/profile/favorites", name="api_user_favorites")
     *
     */
    public function getFavorites()
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if($user->getType() != User::TYPE_CLIENT){
            return $this->helper->error('En tant que restaurateur, vous ne pouvez pas effectuer cette action');
        }
        $elasticaManager = $this->container->get('fos_elastica.manager');
        $favorites = $user->getFavorites();
        $json=array();
        foreach($favorites as $favorite){
            $reservations = $elasticaManager->getRepository('AppBundle:Reservation')->findByClientAndRestaurant($user,$favorite);
            $ordersCount = count($reservations);
            $json[]=array(
                "ordersCount" => $ordersCount,
                "restaurant" => $favorite
            );
        }
        if(!isset($json[0]))
            $json[]=array();

        return $this->helper->success($json, 200);
    }

    /**
     * @REST\Get("/profile/favoritesids", name="api_user_favorites_ids")
     *
     */
    public function getFavoritesIds()
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if($user->getType() != User::TYPE_CLIENT){
            return $this->helper->error('En tant que restaurateur, vous ne pouvez pas effectuer cette action');
        }
        $elasticaManager = $this->container->get('fos_elastica.manager');
        $favorites = $user->getFavorites();
        $json=array();
        foreach($favorites as $favorite){
            $reservations = $elasticaManager->getRepository('AppBundle:Reservation')->findByClientAndRestaurant($user,$favorite);
            array_push($json,$favorite->getId());
        }
        if(!isset($json[0]))
            $json[]=array();

        return $this->helper->success($json, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/favorites/add", name="api_user_add_favorite")
     *
     */
    public function addFavorite(Request $request)
    {
        $fosUserManager = $this->get('fos_user.user_manager');
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if($user->getType() != User::TYPE_CLIENT){
            return $this->helper->error('En tant que restaurateur, vous ne pouvez pas effectuer cette action');
        }
        if(!$restaurant)
            return $this->helper->elementNotFound('Restaurant', 404);
        $user->addFavorite($restaurant);
        $fosUserManager->updateUser($user);
        return $this->helper->success($user, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/favorites/remove", name="api_user_remove_favorite")
     *
     */
    public function removeFavorite(Request $request)
    {
        $fosUserManager = $this->get('fos_user.user_manager');
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if($user->getType() != User::TYPE_CLIENT){
            return $this->helper->error('En tant que restaurateur, vous ne pouvez pas effectuer cette action');
        }
        if(!$restaurant)
            return $this->helper->elementNotFound('Restaurant', 404);

        $user->removeFavorite($restaurant);
        $fosUserManager->updateUser($user);
        return $this->helper->success($user, 200);
    }

    /**
     *
     * @REST\Post("/restaurants/{id}/reservations/{idReservation}/note", name="api_user_update_note")
     *
     */
    public function updateNote(Request $request)
    {
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $elasticaManager = $this->container->get('fos_elastica.manager');


        if($user->getType() != User::TYPE_CLIENT){
            return $this->helper->error('En tant que restaurateur, vous ne pouvez pas effectuer cette action');
        }
        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));
        if (!$restaurant instanceof Restaurant) {
            return $this->helper->elementNotFound('Restaurant', 404);
        }
        $reservation = $elasticaManager->getRepository('AppBundle:Reservation')->findById($request->get('idReservation'));
        if (!$reservation instanceof Reservation) {
            return $this->helper->elementNotFound('Reservation', 404);
        }
        $note = $this->getNoteRepository()->findOneBy(array('restaurant' => $restaurant, 'user' => $user, 'reservation' => $reservation));
        $em = $this->getEntityManager();

        if(!($note)){
            $currentNote =$request->get('note');
            if($currentNote > 0 && $currentNote< 6) {
                $note = new Note();
                $note->setUser($user);
                $note->setRestaurant($restaurant);
                $note->setReservation($reservation);
                $note->setStatus(1);
                $note->setNote($currentNote);
                $em->persist($note);
                $em->flush();
            }
            else{
                return $this->helper->error('La note doit être comprise entre 1 et 5');

            }
        }
        else
            return $this->helper->error('Vous avez déjà noté cette commande.');




        $newAverage = 0;
        $count = 0;
        $notes = $this->getNoteRepository()->findBy(array('restaurant' => $restaurant));

        foreach ($notes as $note){
            $newAverage = $newAverage + $note->getNote();
            $count = $count + 1;
        }

        $restaurant->setAverageNote($newAverage/$count);

        $em->persist($restaurant);
        $em->flush();
        return $this->helper->success($user, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/reservations/{idReservation}/note", name="api_user_has_note")
     *
     */
    public function hasNote(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if($user->getType() != User::TYPE_CLIENT){
            return $this->helper->error('En tant que restaurateur, vous ne pouvez pas effectuer cette action');
        }
        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));
        $reservation = $elasticaManager->getRepository('AppBundle:Reservation')->findById($request->get('idReservation'));
        if (!$restaurant instanceof Restaurant) {
            return $this->helper->elementNotFound('Restaurant', 404);
        }
        $reservation = $elasticaManager->getRepository('AppBundle:Reservation')->findById($request->get('idReservation'));
        if (!$reservation instanceof Reservation) {
            return $this->helper->elementNotFound('Reservation', 404);
        }
        $note = $this->getNoteRepository()->findOneBy(array('restaurant' => $restaurant, 'user' => $user, 'reservation' => $reservation));
        $verif=false;

        if($note){
            $verif=true;
        }

        $json=array("hasNote" => $verif);

        return $this->helper->success($json, 200);
    }

    /**
     * @REST\Get("/profile/reservations", name="api_list_client_reservations")
     */
    public function getClientReservations(Request $request, ParamFetcher $paramFetcher) {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        $elasticaManager = $this->container->get('fos_elastica.manager');

        $reservations = $elasticaManager->getRepository('AppBundle:Reservation')->findByClient($user);
        if($reservations) {
            foreach ($reservations as $reservation) {
                $userFavorites = $reservation->getUser()->getFavorites();
                $note = $this->getNoteRepository()->findOneBy(array('restaurant' => $reservation->getRestaurant(), 'user' => $user, 'reservation' => $reservation));
                $verif = false;

                if ($note) {
                    $verif = true;
                }
                $jsonContents = array();
                $contents = $elasticaManager->getRepository('AppBundle:ReservationContent')->findByReservation($reservation);

                $lastSeat = array();
                if (is_array($contents)) {

                    foreach ($contents as $contentSeat) {

                        $allContents = $elasticaManager->getRepository('AppBundle:ReservationContent')->findBySeat($contentSeat->getSeat());
                        $currentSeat = $contentSeat->getSeat()->getId();
                        foreach ($allContents as $content) {
                            if ($currentSeat != $lastSeat) {
                                $jsonContents[] = array(
                                    "id" => $content->getContent()->getId(),
                                    "name" => $content->getContent()->getName(),
                                    "quantity" => $content->getQuantity(),
                                    "totalPrice" => $content->getTotalPrice()
                                );
                            }
                        }
                        if (!in_array($currentSeat, $lastSeat)) {
                            $seatArray[] = array(
                                "name" => $contentSeat->getSeat()->getName(),
                                "content" => $jsonContents
                            );
                        }
                        array_push($lastSeat, $currentSeat);
                        $jsonContents = array();
                    }
                    $reservationArray[] = array(
                        "id" => $reservation->getId(),
                        "date" => $reservation->getDate(),
                        "nbParticipants" => $reservation->getNbParticipants(),
                        "total" => $reservation->getTotal(),
                        "timeStep" => $reservation->getTimeStep(),
                        "state" => $reservation->getState(),
                        "hasNote" => $verif,
                        "user" => array(
                            "id" => $reservation->getUser()->getId(),
                            "lastname" => $reservation->getUser()->getLastName(),
                            "firstname" => $reservation->getUser()->getFirstName(),
                            "phoneNumber" => $reservation->getUser()->getPhoneNumber(),
                        ),
                        "date" => $reservation->getDate(),
                        "restaurant" => array(
                            "id" => $reservation->getRestaurant()->getId(),
                            "name" => $reservation->getRestaurant()->getName(),
                            "picture" => $reservation->getRestaurant()->getPicture(),
                            "favorite" => $userFavorites->contains($reservation->getRestaurant()) ? true : false

                        ),
                        "seats" => $seatArray,
                    );
                    $seatArray = array();
                }
            }
        }
        else
            $reservationArray[]=array();


        return $this->helper->success($reservationArray, 200);
    }

    /**
     * @REST\Get("/profile/reservations/{idReservation}", name="api_show_reservation")
     */
    public function getReservation(Request $request) {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        $elasticaManager = $this->container->get('fos_elastica.manager');

        $reservation = $elasticaManager->getRepository('AppBundle:Reservation')->findById($request->get('idReservation'));
        if (!$reservation) {
            return $this->helper->elementNotFound('Reservation');
        }
        if($reservation->getUser() != $user){
            return $this->helper->error("Cette réservation n'est pas la vôtre");
        }



        $jsonContents=array();
        $restaurant = $reservation->getRestaurant();
        $userFavorites = $reservation->getUser()->getFavorites();
        $contents = $elasticaManager->getRepository('AppBundle:ReservationContent')->findByReservation($reservation);
        $note = $this->getNoteRepository()->findOneBy(array('restaurant' => $reservation->getRestaurant(), 'user' => $user, 'reservation' => $reservation));
        $verif=false;

        if($note){
            $verif=true;
        }
        $lastSeat = array();
        foreach($contents as $contentSeat) {
            $allContents = $elasticaManager->getRepository('AppBundle:ReservationContent')->findBySeat($contentSeat->getSeat());
            $currentSeat=$contentSeat->getSeat()->getId();
            foreach ($allContents as $content) {
                if($currentSeat!=$lastSeat) {
                    $jsonContents[] = array(
                        "id" => $content->getContent()->getId(),
                        "name" => $content->getContent()->getName(),
                        "quantity" => $content->getQuantity(),
                        "totalPrice" => $content->getTotalPrice()
                    );
                }
            }
            if(!in_array($currentSeat,$lastSeat)) {
                $seatArray[] = array(
                    "name" => $contentSeat->getSeat()->getName(),
                    "content" => $jsonContents
                );
            }
            array_push($lastSeat,$currentSeat);
            $jsonContents = array();
        }
        $reservationArray[] = array(
            "id" => $reservation->getId(),
            "date" => $reservation->getDate(),
            "nbParticipants" => $reservation->getNbParticipants(),
            "total" => $reservation->getTotal(),
            "timeStep" => $reservation->getTimeStep(),
            "state" => $reservation->getState(),
            "hasNote" => $verif,
            "user" => array(
                "id" => $reservation->getUser()->getId(),
                "lastname" => $reservation->getUser()->getLastName(),
                "firstname" => $reservation->getUser()->getFirstName(),
                "phoneNumber" => $reservation->getUser()->getPhoneNumber(),
            ),
            "date" => $reservation->getDate(),
            "restaurant" => array(
                "id" => $reservation->getRestaurant()->getId(),
                "name" => $reservation->getRestaurant()->getName(),
                "picture" => $reservation->getRestaurant()->getPicture(),
                "favorite" => $userFavorites->contains($restaurant) ? true : false
            ),
            "seats" => $seatArray,
        );

        return $this->helper->success($reservationArray, 200);
    }
}