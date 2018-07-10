<?php

namespace AppBundle\API\Client;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Client;
use AppBundle\Entity\Note;
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
        $favorites = $user->getFavorites();
        return $this->helper->success($favorites, 200);
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
        $user->removeFavorite($restaurant);
        $fosUserManager->updateUser($user);
        return $this->helper->success($user, 200);
    }

    /**
     *
     * @REST\Put("/restaurants/{id}/note", name="api_user_update_note")
     *
     */
    public function updateNote(Request $request)
    {
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $note = $this->getNoteRepository()->findOneBy(array('restaurant' => $restaurant, 'user' => $user));
        if($user->getType() != User::TYPE_CLIENT){
            return $this->helper->error('En tant que restaurateur, vous ne pouvez pas effectuer cette action');
        }
        if($note == null){
            $note = new Note();
            $note->setUser($user);
            $note->setRestaurant($restaurant);
            $note->setStatus(1);
        }

        $note->setNote($request->get('note'));
        $em = $this->getEntityManager();
        $em->persist($note);
        $em->flush();

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
     * @REST\Get("/profile/reservations", name="api_list_client_reservations")
     */
    public function getClientReservations(Request $request, ParamFetcher $paramFetcher) {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        $elasticaManager = $this->container->get('fos_elastica.manager');

        $reservations = $elasticaManager->getRepository('AppBundle:Reservation')->findByClient($user);

        $json = array();
        foreach($reservations as $reservation){
            $restaurant = $reservation->getRestaurant();
            $userFavorites = $reservation->getUser()->getFavorites();
            $jsonContents=array();
            $contents = $elasticaManager->getRepository('AppBundle:ReservationContent')->findByReservation($reservation);
            foreach($contents as $content) {
                $jsonContents[]=array(
                    "id" => $content->getId(),
                    "idCont" => $content->getContent()->getId(),
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
                    "favorite" => $userFavorites->contains($restaurant) ? true : false
                ),
                "content" => $jsonContents,

            );
        }


        return $this->helper->success($json, 200);
    }

    /**
     * @REST\Get("/profile/reservations/{idReservation}", name="api_show_reservation")
     */
    public function getReservation(Request $request) {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        $elasticaManager = $this->container->get('fos_elastica.manager');

        $reservation = $elasticaManager->getRepository('AppBundle:Reservation')->findById($request->get('idReservation'));
        if($reservation->getUser() != $user){
            return $this->helper->error("Cette réservation n'est pas la vôtre");
        }

        $jsonContents=array();
        $restaurant = $reservation->getRestaurant();
        $userFavorites = $reservation->getUser()->getFavorites();
        $contents = $elasticaManager->getRepository('AppBundle:ReservationContent')->findByReservation($reservation);
        foreach($contents as $content) {
            $jsonContents[]=array(
                "id" => $content->getId(),
                "idCont" => $content->getContent()->getId(),
                "name" => $content->getContent()->getName(),
                "quantity" => $content->getQuantity(),
                "totalPrice" => $content->getTotalPrice()
            );
        }
        $reservation = array(
            "id" => $reservation->getId(),
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
            "content" => $jsonContents,
        );

        return $this->helper->success($reservation, 200);
    }
}