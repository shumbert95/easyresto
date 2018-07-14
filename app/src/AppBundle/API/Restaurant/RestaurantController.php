<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Restaurant;
use AppBundle\Entity\User;
use AppBundle\Form\RestaurantType;
use AppBundle\Model\RestaurantSearch;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @REST\RequestParam(name="region", nullable=true)
     * @REST\RequestParam(name="website", nullable=true)
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

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $restaurant = new Restaurant();

        $params = $paramFetcher->all();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }

        if (isset($params['picture'])){
            $restaurant->setPicture($request->get('picture'));
        }

        $restaurant->setStatus(Restaurant::STATUS_ONLINE);
        $restaurant->setOpen(0);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();


        $em = $this->getEntityManager();
        $em->persist($restaurant);
        $em->flush();

        return $this->helper->success($restaurant, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/users/{idUser}/add", name="api_add_user_restaurant")
     *
     */
    public function addUserRestaurant(Request $request)
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        $restaurantUsers = $restaurant->getUsers();

        $user=$this->getUserRepository()->findOneBy(array("id" => $request->get('idUser')));

        if($restaurantUsers->contains($user))
            return $this->helper->error('L\'utilisateur est déjà manager du restaurant');

        if($user->getType()!=User::TYPE_RESTORER)
            return $this->helper->error('L\'utilisateur n\'est pas un restaurateur');

        $restaurant->addUser($user);
        $em = $this->getEntityManager();
        $em->persist($restaurant);
        $em->flush();

        return $this->helper->success("L'utilisateur a bien été ajouté",200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/users/{idUser}/remove", name="api_remove_user_restaurant")
     *
     */
    public function removeUserRestaurant(Request $request)
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        $restaurantUsers = $restaurant->getUsers();

        $user=$this->getUserRepository()->findOneBy(array("id" => $request->get('idUser')));

        if(!$restaurantUsers->contains($user))
            return $this->helper->error('L\'utilisateur ne fait pas parti du restaurant');

        if($user->getType()!=User::TYPE_RESTORER)
            return $this->helper->error('L\'utilisateur n\'est pas un restaurateur');

        $restaurant->removeUser($user);
        $em = $this->getEntityManager();
        $em->persist($restaurant);
        $em->flush();

        return $this->helper->success("L'utilisateur a bien été retiré",200);
    }

    /**
     *
     * @REST\Put("/restaurants/{id}", name="api_edit_restaurant")
     *
     */
    public function editRestaurant(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');

        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }
        $request_data = $request->request->all();


        if(isset($request_data['name'])){
            $restaurant->setName($request_data['name']);
        }
        if(isset($request_data['city'])){
            $restaurant->setCity($request_data['city']);
        }
        if(isset($request_data['postalCode'])){
            $restaurant->setPostalCode($request_data['postalCode']);
        }
        if(isset($request_data['address'])){
            $restaurant->setAddress($request_data['address']);
        }
        if(isset($request_data['region'])){
            $restaurant->setRegion($request_data['region']);
        }
        if(isset($request_data['phone'])){
            $restaurant->setPhone($request_data['phone']);
        }
        if(isset($request_data['description'])){
            $restaurant->setDescription($request_data['description']);
        }
        if(isset($request_data['picture'])){
            $restaurant->setPicture($request_data['picture']);
        }
        if(isset($request_data['website'])){
            $restaurant->setWebsite($request_data['website']);
        }
        if(isset($request_data['seats'])){
            $restaurant->setSeats($request_data['seats']);
        }
        if(isset($request_data['latitude'])){
            $restaurant->setLatitude($request_data['latitude']);
        }
        if(isset($request_data['longitude'])){
            $restaurant->setLongitude($request_data['longitude']);
        }
        if(isset($request_data['open'])){
            $restaurant->setOpen($request_data['open']);
        }

        if(isset($request_data['moments'])){
            $moments = $request_data['moments'];
            $arrayMoments = new ArrayCollection();
            foreach($moments as $momentId){
                $moment = $elasticaManager->getRepository('AppBundle:Moment')->findById($momentId["id"]);
                if($moment && !$arrayMoments->contains($moment)){
                    $arrayMoments->add($moment);
                }
            }
            $restaurant->setMoments($arrayMoments);
        }

        if(isset($request_data['categories'])){
            $categories = $request_data['categories'];
            $arrayCategories = new ArrayCollection();
            foreach($categories as $categoryId){
                $category = $elasticaManager->getRepository('AppBundle:CategoryRestaurant')->findById($categoryId["id"]);
                if($category && !$arrayCategories->contains($category)){
                    $arrayCategories->add($category);
                }
            }
            $restaurant->setCategories($arrayCategories);
        }

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
        if (!$restaurant instanceof Restaurant) {
            return $this->helper->elementNotFound('Restaurant', 404);
        }
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


        $schedule = json_encode($request_data['schedule']);
        $restaurant->setSchedule($schedule);
        $this->getEntityManager()->persist($restaurant);
        $this->getEntityManager()->flush();

        return $this->helper->success($restaurant, 200);
    }

    /**
     * @param Request $request
     *
     * @REST\Put("/restaurants/{id}/categories", name="api_update_restaurant_categories")
     */
    public function updateCategories(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        if(!$restaurant){
            return $this->helper->elementNotFound('Restaurant');

        }
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }
        $elasticaManager = $this->container->get('fos_elastica.manager');

        $request_data = $request->request->all();
        if(isset($request_data['categories'])){
            $categories = $request_data['categories'];
            $arrayCategories = new ArrayCollection();
            foreach($categories as $categoryId){
                $category = $elasticaManager->getRepository('AppBundle:CategoryRestaurant')->findById($categoryId["id"]);
                if($category && !$arrayCategories->contains($category)){
                    $arrayCategories->add($category);
                }
            }
            $restaurant->setCategories($arrayCategories);
        }

        $em = $this->getEntityManager();
        $em->persist($restaurant);
        $em->flush();

        return $this->helper->success($restaurant, 200);
    }

    /**
     * @param Request $request
     *
     * @REST\Put("/restaurants/{id}/moments", name="api_update_restaurant_moments")
     */
    public function updateMoments(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        if(!$restaurant){
            return $this->helper->elementNotFound('Restaurant');

        }
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }
        $elasticaManager = $this->container->get('fos_elastica.manager');

        $request_data = $request->request->all();
        if(isset($request_data['moments'])){
            $moments = $request_data['moments'];
            $arrayMoments = new ArrayCollection();
            foreach($moments as $momentId){
                $moment = $elasticaManager->getRepository('AppBundle:Moment')->findById($momentId["id"]);
                if($moment && !$arrayMoments->contains($moment)){
                    $arrayMoments->add($moment);
                }
            }
            $restaurant->setMoments($arrayMoments);
        }

        $em = $this->getEntityManager();
        $em->persist($restaurant);
        $em->flush();

        return $this->helper->success($restaurant, 200);
    }

    /**
     * @param Request $request
     *
     * @REST\Put("/restaurants/{id}/categories_moments", name="api_update_restaurant_moments_categories")
     */
    public function updateCategoriesAndMoments(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        if(!$restaurant){
            return $this->helper->elementNotFound('Restaurant');

        }
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }
        $elasticaManager = $this->container->get('fos_elastica.manager');

        $request_data = $request->request->all();
        if(isset($request_data['moments'])){
            $moments = $request_data['moments'];
            $arrayMoments = new ArrayCollection();
            foreach($moments as $momentId){
                $moment = $elasticaManager->getRepository('AppBundle:Moment')->findById($momentId["id"]);
                if($moment && !$arrayMoments->contains($moment)){
                    $arrayMoments->add($moment);
                }
            }
            $restaurant->setMoments($arrayMoments);
        }

        if(isset($request_data['categories'])){
            $categories = $request_data['categories'];
            $arrayCategories = new ArrayCollection();
            foreach($categories as $categoryId){
                $category = $elasticaManager->getRepository('AppBundle:CategoryRestaurant')->findById($categoryId["id"]);
                if($category && !$arrayCategories->contains($category)){
                    $arrayCategories->add($category);
                }
            }
            $restaurant->setCategories($arrayCategories);
        }

        $em = $this->getEntityManager();
        $em->persist($restaurant);
        $em->flush();

        return $this->helper->success($restaurant, 200);
    }


    /**
     * @QueryParam(name="latitude", nullable=false)
     * @QueryParam(name="longitude", nullable=false)
     * @QueryParam(name="exact", nullable=false)
     * @QueryParam(name="categories", nullable=true)
     * @QueryParam(name="name", nullable=true)
     * @QueryParam(name="moments", nullable=true)
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
        $restaurantSearch->setCategory($params['categories']);
        $restaurantSearch->setName($params['name']);
        $restaurantSearch->setMoment($params['moments']);
        $elasticaManager = $this->container->get('fos_elastica.manager');

        $results = $elasticaManager->getRepository('AppBundle:Restaurant')->search($restaurantSearch);

        if (!$results) {
            return $this->helper->empty();
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

    /**
     * @param Request $request
     *
     * @REST\Get("/restaurants/{id}/schedule", name="api_get_schedule")
     */
    public function getRestaurantSchedule(Request $request)
    {
        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        if (!$restaurant instanceof Restaurant) {
            return $this->helper->elementNotFound('Restaurant', 404);
        }
        if(!$restaurant->getSchedule())
            return $this->helper->elementNotFound('Schedule', 404);
        $schedule = json_decode($restaurant->getSchedule(),true);

        return $this->helper->success($schedule, 200);
    }




}