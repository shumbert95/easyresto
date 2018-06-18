<?php

namespace AppBundle\API;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Restaurant;
use AppBundle\Entity\User;
use AppBundle\Form\RegistrationClientType;
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

        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        $restaurant->addUser($user);

        $em = $this->getEntityManager();
        $em->persist($restaurant);
        $em->flush();

        return $this->helper->success($restaurant, 200);
    }

    /**
     * @param Request $request
     *
     * @return View
     *
     * @REST\Post("/restaurant/schedule", name="api_update_schedule")
     */
    public function updateSchedule(Request $request)
    {
        $errors = array();
        $request_data = $request->request->all();

        if (!isset($request_data['id']) || empty($request_data['id'])) {
            $errors[] = 'Missing parameter "id"';
        }

        if (!$request_data['schedule']) {
            $errors[] = 'Missing parameter "schedule"';
        }

        if (count($errors)) {
            return $this->helper->error($errors, 400);
        }

        $restaurant = $this->getRestaurantRepository()->find($request_data['id']);

        if (!$restaurant instanceof Restaurant) {
            $this->helper->elementNotFound('Restaurant', 404);
        }

        $restaurant->setSchedule($request_data['schedule']);

        $this->getEntityManager()->persist($restaurant);
        $this->getEntityManager()->flush();

        return $this->helper->success($restaurant, 200);
    }

    /**
     * @return View
     *
     * @REST\Get("/restaurants", name="api_list_restaurants")
     *
     */
    public function getRestaurants()
    {
        $restaurants = $this->getRestaurantRepository()->findAll();
        return $this->helper->success($restaurants, 200);
    }

    /**
     *
     * @REST\Get("/restaurant/{id}", name="api_detail_restaurant")
     *
     */
    public function getRestaurant(Request $request)
    {
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        return $this->helper->success($restaurant, 200);
    }
}