<?php

namespace AppBundle\API;

use AppBundle\Helper\ApiHelper;
use FOS\RestBundle\Controller\FOSRestController;

class ApiBaseController extends FOSRestController
{
    /**
     * @var ApiHelper
     */
    public $helper;

    public function __construct()
    {
        $this->helper = new ApiHelper();
    }

    public function getEntityManager() {
        return $this->getDoctrine()->getEntityManager();
    }

    public function getUserRepository() {
        return $this->getDoctrine()->getRepository('AppBundle:User');
    }

    public function getRestaurantRepository() {
        return $this->getDoctrine()->getRepository('AppBundle:Restaurant');
    }

    public function getNoteRepository() {
        return $this->getDoctrine()->getRepository('AppBundle:Note');
    }

    public function getCategoryRestaurantRepository() {
        return $this->getDoctrine()->getRepository('AppBundle:CategoryRestaurant');
    }

    public function getTabMealRepository() {
        return $this->getDoctrine()->getRepository('AppBundle:TabMeal');
    }

    public function getContentRepository() {
        return $this->getDoctrine()->getRepository('AppBundle:Content');
    }

    public function getIngredientRepository() {
        return $this->getDoctrine()->getRepository('AppBundle:Ingredient');
    }

    public function getMealSetElementRepository() {
        return $this->getDoctrine()->getRepository('AppBundle:MealSetElement');
    }

    public function getReservationRepository() {
        return $this->getDoctrine()->getRepository('AppBundle:Reservation');
    }

    public function getReservationContentRepository() {
        return $this->getDoctrine()->getRepository('AppBundle:ReservationContent');
    }


}