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
        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $params = $paramFetcher->all();

        $tab = $this->getTabMealRepository()->find($request->get('idTab'));

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
        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $meal=$this->getContentRepository()->findOneBy(array("id" => $request->get('idMeal'), "restaurant" => $restaurant));


        $request_data = $request->request->all();

        if($request_data['name'] != null){
            $meal->setName($request_data['name']);
        }
        if($request_data['price'] != null){
            $meal->setPrice($request_data['price']);
        }
        if($request_data['description'] != null){
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
    public function updateDailyStock(Request $request,ParamFetcher $paramFetcher)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }
        $params=$paramFetcher->all();
        $meal = $this->getContentRepository()->findOneBy(array('id' => $request->get('idMeal'), 'restaurant' => $restaurant, 'status'=> true));

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
        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }
        $params=$paramFetcher->all();
        $meal = $this->getContentRepository()->findOneBy(array('id' => $request->get('idMeal'), 'restaurant' => $restaurant, 'status'=> true));

        $meal->setCurrentStock($meal->getCurrentStock() + $params['stock']);

        $em = $this->getEntityManager();
        $em->persist($meal);
        $em->flush();

        return $this->helper->success($meal, 200);
    }


    /**
     * @REST\Delete("/restaurants/{id}/meals/{idMeal}", name="api_delete_meal")
     */
    public function deleteMeal(Request $request)
    {
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $meal = $this->getContentRepository()->findOneBy(
            array(
                'restaurant' => $restaurant,
                'id' => $request->get('idMeal')
            ));

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
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $meals = $this->getContentRepository()->findBy(array('restaurant' => $restaurant,'type' => Content::TYPE_MEAL));
        return $this->helper->success($meals, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/tabs/{idTab}/meals", name="api_list_meals_by_tab")
     *
     */
    public function getMealsFromTab(Request $request)
    {
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $tab = $this->getTabMealRepository()->find($request->get('idTab'));

        $meals = $this->getContentRepository()->findBy(array(
            'restaurant' => $restaurant,
            'tab' => $tab,
            'type' => Content::TYPE_MEAL
        ));

        return $this->helper->success($meals, 200);
    }





}