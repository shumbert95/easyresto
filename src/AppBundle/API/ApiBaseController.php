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
}