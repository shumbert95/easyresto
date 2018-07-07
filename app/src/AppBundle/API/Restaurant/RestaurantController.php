<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Restaurant;
use AppBundle\Entity\User;
use AppBundle\Form\RestaurantType;
use AppBundle\Model\RestaurantSearch;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Controller\Annotations\QueryParam;


class RestaurantController extends ApiBaseController
{
    /**
     * @param ParamFetcher $paramFetcher
     *
     * @REST\Post("/restaurants/create", name="api_create_restaurant")
     * @REST\RequestParam(name="name")
     * @REST\RequestParam(name="address")
     * @REST\RequestParam(name="addressComplement", nullable=true)
     * @REST\RequestParam(name="city")
     * @REST\RequestParam(name="postalCode")
     * @REST\RequestParam(name="latitude")
     * @REST\RequestParam(name="longitude")
     * @REST\RequestParam(name="phone")
     * @REST\RequestParam(name="description")
     * @REST\RequestParam(name="seats", nullable=true)
     */
    public function createRestaurant(ParamFetcher $paramFetcher, Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            ($user->getType()!= User::TYPE_RESTORER)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $restaurant = new Restaurant();

        $params = $paramFetcher->all();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }

        if ($request->get('picture') != null){
            $restaurant->setPicture($request->get('picture'));
        }

        $restaurant->setStatus(Restaurant::STATUS_ONLINE);
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
     * @REST\Put("/restaurants/{id}/schedule", name="api_update_schedule")
     */
    public function updateSchedule(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $errors = array();
        $request_data = $request->request->all();

        if (!$request_data['schedule']) {
            return $this->helper->error('schedule', true);
        }

        if (!$restaurant instanceof Restaurant) {
            return $this->helper->elementNotFound('Restaurant', 404);
        }

        $restaurant->setSchedule($request_data['schedule']);

        $this->getEntityManager()->persist($restaurant);
        $this->getEntityManager()->flush();

        return $this->helper->success($restaurant, 200);
    }

    /**
     * @param Request $request
     *
     * @REST\Put("/restaurants/{id}/categories/{idCat}/add", name="api_add_category")
     */
    public function addCategory(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }
        $category = $this->getCategoryRestaurantRepository()->findOneBy(array("id" => $request->get('idCat')));
        $restaurant->addCategory($category);
        $this->getEntityManager()->persist($restaurant);
        $this->getEntityManager()->flush();

        return $this->helper->success($restaurant, 200);
    }

    /**
     * @param Request $request
     *
     * @REST\Put("/restaurants/{id}/categories/{idCat}/remove", name="api_remove_category")
     */
    public function removeCategory(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }
        $category = $this->getCategoryRestaurantRepository()->findOneBy(array("id" => $request->get('idCat')));
        $restaurant->removeCategory($category);
        $this->getEntityManager()->persist($restaurant);
        $this->getEntityManager()->flush();

        return $this->helper->success($restaurant, 200);
    }



    /**
     * @QueryParam(name="latitude", nullable=false)
     * @QueryParam(name="longitude", nullable=false)
     * @QueryParam(name="exact", nullable=false)
     *
     * @REST\Get("/restaurants", name="api_list_restaurants")
     *
     */
    public function getRestaurants(ParamFetcher $paramFetcher)
    {
        $params = $paramFetcher->all();
        $restaurantSearch = new RestaurantSearch();

        if (!$params['longitude']) {
            return $this->helper->error('longitude', true);
        }

        if (!$params['latitude']) {
            return $this->helper->error('latitude', true);
        }

        if (!isset($params['exact'])) {
            return $this->helper->error('exact', true);
        }

        $restaurantSearch->setLatitude($params['latitude']);
        $restaurantSearch->setLongitude($params['longitude']);
        $restaurantSearch->setExact((bool)$params['exact']);

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $results = $elasticaManager->getRepository('AppBundle:Restaurant')->search($restaurantSearch);

        if (!$results) {
            $this->helper->elementNotFound('Restaurants');
        }

        return $this->helper->success($results, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}", name="api_detail_restaurant")
     *
     */
    public function getRestaurant(Request $request)
    {
        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $result = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));

        if (!$result) {
            return $this->helper->elementNotFound('Restaurant');
        } else {
            return $this->helper->success($result, 200);
        }
    }




}