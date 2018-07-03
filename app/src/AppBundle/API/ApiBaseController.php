<?php

namespace AppBundle\API;

use AppBundle\Helper\ApiHelper;
use FOS\RestBundle\Controller\Annotations as REST;
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

    public function getCategoryMealRepository() {
        return $this->getDoctrine()->getRepository('AppBundle:CategoryMeal');
    }

    public function getTabMealRepository() {
        return $this->getDoctrine()->getRepository('AppBundle:TabMeal');
    }

    public function getMealRepository() {
        return $this->getDoctrine()->getRepository('AppBundle:Meal');
    }

    public function getMenuRepository() {
        return $this->getDoctrine()->getRepository('AppBundle:Menu');
    }

    public function getClientRepository() {
        return $this->getDoctrine()->getRepository('AppBundle:Client');
    }
}