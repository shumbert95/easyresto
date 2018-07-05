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
        return $this->helper->success($user, 200);
    }


    /**
     * @REST\Get("/profile/favorites", name="api_user_favorites")
     *
     */
    public function getFavorites()
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $favorites = $user->getFavorites();
        return $this->helper->success($favorites, 200);
    }

    /**
     *
     * @REST\Put("/restaurants/{id}/favorites/add", name="api_user_add_favorite")
     *
     */
    public function addFavorite(Request $request)
    {
        $fosUserManager = $this->get('fos_user.user_manager');
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $user->addFavorite($restaurant);
        $fosUserManager->updateUser($user);
        return $this->helper->success($user, 200);
    }

    /**
     *
     * @REST\Put("/restaurants/{id}/favorites/remove", name="api_user_remove_favorite")
     *
     */
    public function removeFavorite(Request $request)
    {
        $fosUserManager = $this->get('fos_user.user_manager');
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
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
}