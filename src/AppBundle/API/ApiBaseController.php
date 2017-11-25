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
}