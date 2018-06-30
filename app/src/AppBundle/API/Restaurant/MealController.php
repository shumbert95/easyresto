<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\CategoryMeal;
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
     * @REST\Post("/restaurant/{id}/category/{idCat}/meal/create", name="api_create_meal")
     * @REST\RequestParam(name="name")
     * @REST\RequestParam(name="description")
     * @REST\RequestParam(name="price")
     * @REST\RequestParam(name="availability")
     * @REST\RequestParam(name="initialStock")
     * @REST\RequestParam(name="currentStock")
     * @REST\RequestParam(name="position")
     *
     */
    public function createMeal(Request $request, ParamFetcher $paramFetcher)
    {
        $params = $paramFetcher->all();

        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $category = $this->getCategoryMealRepository()->find($request->get('idCat'));


        $meal = $this->getMealRepository()->findOneBy(array('name' => $params['name'], 'status' => true));

        if ($meal instanceof Meal && $meal->isStatus()) {
            return $this->helper->error('This name is already used');
        }
        else if (($meal instanceof Meal && !$meal->getStatus())) {
            $meal->setStatus(1);
        }
        else if(!($meal instanceof Meal)) {
            $meal = new Meal();
            $meal->setStatus(1);
            $meal->setRestaurant($restaurant);
            $meal->setCategory($category);
        }

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
     * @REST\Post("/restaurant/{id}/meal/{idMeal}", name="api_daily_stock")
     * @REST\RequestParam(name="initialStock")
     */
    public function setDailyStock(Request $request,ParamFetcher $paramFetcher)
    {
        $params=$paramFetcher->all();
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $meal = $this->getMealRepository()->findOneBy(array('id' => $request->get('idMeal'), 'restaurant' => $restaurant, 'status'=> true));

        $meal->setInitialStock($params['initialStock']);
        $meal->setCurrentStock($params['initialStock']);
        $em = $this->getEntityManager();
        $em->persist($meal);
        $em->flush();

        return $this->helper->success($meal, 200);
    }

    /**
     *
     * @REST\Post("/restaurant/{id}/meal/{idMeal}/stock", name="api_stock_change")
     * @REST\RequestParam(name="stock")
     */
    public function setCurrentStock(Request $request,ParamFetcher $paramFetcher)
    {
        $params=$paramFetcher->all();
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $meal = $this->getMealRepository()->findOneBy(array('id' => $request->get('idMeal'), 'restaurant' => $restaurant, 'status'=> true));

        $meal->setCurrentStock($meal->getCurrentStock() + $params['stock']);

        $em = $this->getEntityManager();
        $em->persist($meal);
        $em->flush();

        return $this->helper->success($meal, 200);
    }

    /**
     *
     * @REST\Post("/restaurant/{id}/meal/{idMeal}/position", name="api_update_meal_position")
     * @REST\RequestParam(name="position")
     */
    public function updateMealPosition(Request $request, ParamFetcher $paramFetcher)
    {
        $params = $paramFetcher->all();
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $meal = $this->getMealRepository()->findOneBy(
            array(
                'status' => true,
                'restaurant' => $restaurant,
                'id' => $request->get('idMeal')
            ));

        $meal->setPosition($params['position']);
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
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $meal = $this->getMealRepository()->findBy(array('id' => $request->get('idMeal'), 'restaurant' => $restaurant, 'status'=> true));


        return $this->helper->success($meal, 200);
    }

    /**
     * @REST\Delete("/restaurant/{id}/meal/{idMeal}/delete", name="api_delete_meal")
     */
    public function deleteMeal(Request $request)
    {
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $meal = $this->getMealRepository()->findOneBy(
            array(
                'status' => true,
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
     * @REST\Get("/restaurant/{id}/meals", name="api_list_meals")
     *
     */
    public function getMeals(Request $request)
    {
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $meals = $this->getMealRepository()->findBy(array('restaurant' => $restaurant,'status' => true));
        return $this->helper->success($meals, 200);
    }

    /**
     *
     * @REST\Get("/restaurant/{id}/category/{idCategory}/meals", name="api_list_meals_by_category")
     *
     */
    public function getMealsFromCategory(Request $request)
    {
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $category = $this->getCategoryMealRepository()->find($request->get('idCategory'));

        $meals = $this->getMealRepository()->findBy(array(
            'restaurant' => $restaurant,
            'status' => true,
            'category' => $category
        ));

        return $this->helper->success($meals, 200);
    }

    /**
     *
     * @REST\Get("/restaurant/{id}/tab/{idTab}/meals", name="api_list_meals_by_tab")
     *
     */
    public function getMealsFromTab(Request $request)
    {
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $tab = $this->getTabMealRepository()->find($request->get('idTab'));

        $meals = $this->getMealRepository()->findBy(array(
            'restaurant' => $restaurant,
            'status' => true,
            'category' => array(
                'tabMeal' => $tab
            )
        ));

        return $this->helper->success($meals, 200);
    }





}