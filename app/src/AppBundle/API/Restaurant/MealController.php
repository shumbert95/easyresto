<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\CategoryMeal;
use AppBundle\Entity\Content;
use AppBundle\Entity\Meal;
use AppBundle\Entity\Menu;
use AppBundle\Form\CategoryMealType;
use AppBundle\Form\MealType;
use AppBundle\Form\MenuType;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;

class MealController extends ApiBaseController
{

    /**
     * @param Request $request
     *
     *
     * @REST\Post("/restaurants/{id}/tabs/{idTab}/meals/create", name="api_create_meal")
     * @REST\RequestParam(name="name")
     * @REST\RequestParam(name="description", nullable=true)
     * @REST\RequestParam(name="price", nullable=true)
     * @REST\RequestParam(name="availability", nullable=true)
     * @REST\RequestParam(name="initialStock", nullable=true)
     * @REST\RequestParam(name="currentStock", nullable=true)
     * @REST\RequestParam(name="position")
     *
     */
    public function createMeal(Request $request, ParamFetcher $paramFetcher)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idTab')) {
            return $this->helper->error('idTab', true);
        } elseif (!preg_match('/\d/', $request->get('idTab'))) {
            return $this->helper->error('param \'idTab\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));
        if (!$restaurant) {
            return $this->helper->elementNotFound('Restaurant');
        }

        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $params = $paramFetcher->all();

        $tab = $elasticaManager->getRepository('AppBundle:TabMeal')->findById($request->get('idTab'));
        if (!$tab) {
            return $this->helper->elementNotFound('TabMeal');
        }

        $meal = new Content();
        $meal->setStatus(1);
        $meal->setType(CONTENT::TYPE_MEAL);
        $meal->setRestaurant($restaurant);
        $meal->setTab($tab);

        $form = $this->createForm(MealType::class, $meal);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }


        $em = $this->getEntityManager();
        $em->persist($meal);
        $em->flush();

        return $this->helper->success($meal, 200);
    }

    /**
     *
     * @REST\Put("/restaurants/{id}/meals/{idMeal}", name="api_edit_meal")
     *
     */
    public function editMeal(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idMeal')) {
            return $this->helper->error('idMeal', true);
        } elseif (!preg_match('/\d/', $request->get('idMeal'))) {
            return $this->helper->error('param \'idMeal\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));
        if (!$restaurant) {
            return $this->helper->elementNotFound('Restaurant');
        }

        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }
        $meal = $elasticaManager->getRepository('AppBundle:Content')->findById($request->get('idMeal'));
        if (!$meal) {
            return $this->helper->elementNotFound('Meal');
        }

        $request_data = $request->request->all();

        if($request_data['name']){
            $meal->setName($request_data['name']);
        }
        if($request_data['price']){
            $meal->setPrice($request_data['price']);
        }
        if($request_data['description']){
            $meal->setDescription($request_data['description']);
        }

        $em = $this->getEntityManager();
        $em->persist($meal);

        return $this->helper->success($meal, 200);
    }

    /**
     *
     * @REST\Put("/restaurants/{id}/meals/{idMeal}/daily_stock", name="api_daily_stock")
     * @REST\RequestParam(name="initialStock")
     */
    public function updateDailyStock(Request $request, ParamFetcher $paramFetcher)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idMeal')) {
            return $this->helper->error('idMeal', true);
        } elseif (!preg_match('/\d/', $request->get('idMeal'))) {
            return $this->helper->error('param \'idMeal\' must be an integer');
        }

        if (!$request->get('initialStock')) {
            return $this->helper->error('initialStock', true);
        } elseif (!preg_match('/\d/', $request->get('initialStock'))) {
            return $this->helper->error('param \'initialStock\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));
        if (!$restaurant) {
            return $this->helper->elementNotFound('Restaurant');
        }

        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $params=$paramFetcher->all();
        $meal = $elasticaManager->getRepository('AppBundle:Content')->findById($request->get('idMeal'));
        if (!$meal) {
            return $this->helper->elementNotFound('Meal');
        }

        $meal->setInitialStock($meal->getCurrentStock() + $params['initialStock']);
        $meal->setCurrentStock($meal->getInitialStock());
        $em = $this->getEntityManager();
        $em->persist($meal);
        $em->flush();

        return $this->helper->success($meal, 200);
    }

    /**
     *
     * @REST\Put("/restaurants/{id}/meals/{idMeal}/update_stock", name="api_stock_change")
     * @REST\RequestParam(name="stock")
     */
    public function updateCurrentStock(Request $request,ParamFetcher $paramFetcher)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idMeal')) {
            return $this->helper->error('idMeal', true);
        } elseif (!preg_match('/\d/', $request->get('idMeal'))) {
            return $this->helper->error('param \'idMeal\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));
        if (!$restaurant) {
            return $this->helper->elementNotFound('Restaurant');
        }

        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $params=$paramFetcher->all();
        $meal = $elasticaManager->getRepository('AppBundle:Content')->findById($request->get('idMeal'));
        if (!$meal) {
            return $this->helper->elementNotFound('Meal');
        }

        $meal->setCurrentStock($meal->getCurrentStock() + $params['stock']);

        $em = $this->getEntityManager();
        $em->persist($meal);
        $em->flush();

        return $this->helper->success($meal, 200);
    }



    /**
     *
     * @REST\Get("/restaurant/{id}/meal/{idMeal}", name="api_show_meal")
     *
     */
    public function getMeal(Request $request)
    {
        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idMeal')) {
            return $this->helper->error('idMeal', true);
        } elseif (!preg_match('/\d/', $request->get('idMeal'))) {
            return $this->helper->error('param \'idMeal\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));
        if (!$restaurant) {
            return $this->helper->elementNotFound('Restaurant');
        }

        $meal = $elasticaManager->getRepository('AppBundle:Content')->findById($request->get('idMeal'));
        if (!$meal) {
            return $this->helper->elementNotFound('Meal');
        }

        return $this->helper->success($meal, 200);
    }
  
    /**
     * @REST\Delete("/restaurants/{id}/meals/{idMeal}", name="api_delete_meal")
     */
    public function deleteMeal(Request $request)
    {
        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idMeal')) {
            return $this->helper->error('idMeal', true);
        } elseif (!preg_match('/\d/', $request->get('idMeal'))) {
            return $this->helper->error('param \'idMeal\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));
        if (!$restaurant) {
            return $this->helper->elementNotFound('Restaurant');
        }

        $meal = $elasticaManager->getRepository('AppBundle:Content')->findById($request->get('idMeal'));
        if (!$meal) {
            return $this->helper->elementNotFound('Meal');
        }

        $em = $this->getEntityManager();
        $em->remove($meal);
        $em->flush();

        return $this->helper->success($meal, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/meals", name="api_list_meals")
     *
     */
    public function getMeals(Request $request)
    {
        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));
        if (!$restaurant) {
            return $this->helper->elementNotFound('Restaurant');
        }

        $meals = $elasticaManager->getRepository('AppBundle:Content')->findByRestaurant($restaurant, Content::TYPE_MEAL);

        return $this->helper->success($meals, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/tabs/{idTab}/meals", name="api_list_meals_by_tab")
     *
     */
    public function getMealsFromTab(Request $request)
    {
        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idTab')) {
            return $this->helper->error('idTab', true);
        } elseif (!preg_match('/\d/', $request->get('idTab'))) {
            return $this->helper->error('param \'idTab\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));
        if (!$restaurant) {
            return $this->helper->elementNotFound('Restaurant');
        }

        $tab = $elasticaManager->getRepository('AppBundle:TabMeal')->findById($request->get('idTab'));
        if (!$tab) {
            return $this->helper->elementNotFound('TabMeal');
        }

        $meals = $elasticaManager->getRepository('AppBundle:Content')->findByTab($tab, Content::TYPE_MEAL);

        return $this->helper->success($meals, 200);
    }





}