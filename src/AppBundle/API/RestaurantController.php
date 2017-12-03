<?php

namespace AppBundle\API;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Restaurant;
use AppBundle\Entity\User;
use AppBundle\Form\RegistrationType;
use AppBundle\Form\RestaurantType;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RestaurantController extends ApiBaseController
{
    /**
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @REST\Post("/restaurant/create", name="api_create_restaurant")
     * @REST\RequestParam(name="name")
     * @REST\RequestParam(name="address")
     * @REST\RequestParam(name="addressComplement", nullable=true)
     * @REST\RequestParam(name="city")
     * @REST\RequestParam(name="postalCode")
     * @REST\RequestParam(name="phone")
     * @REST\RequestParam(name="description")
     */
    public function createRestaurant(ParamFetcher $paramFetcher)
    {

        $restaurant = new Restaurant();

        $params = $paramFetcher->all();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }

        $restaurant->setStatus(1);
        $restaurant->setOpen(1);


        $em = $this->getEntityManager();
        $em->persist($restaurant);
        $em->flush();

        return $this->helper->success($restaurant, 200);
    }
}