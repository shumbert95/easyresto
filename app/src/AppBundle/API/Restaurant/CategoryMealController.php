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

class CategoryMealController extends ApiBaseController
{
    /**
     * @param Request $request
     *
     *
     * @REST\Post("/restaurant/{id}/tab/{idTab}/category/create", name="api_create_meal_category")
     * @REST\RequestParam(name="name")
     * @REST\RequestParam(name="position")
     */
    public function createMealCategory(Request $request, ParamFetcher $paramFetcher)
    {
        $params = $paramFetcher->all();

        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $tab = $this->getTabMealRepository()->find($request->get('idTab'));


        $category = $this->getCategoryMealRepository()->findOneBy(array('name' => $params['name']));

        if ($category instanceof CategoryMeal && $category->getStatus()) {
            return $this->helper->error('This name is already used');
        }
        else if (($category instanceof CategoryMeal && !$category->getStatus())) {
            $category->setStatus(1);
            $category->setTabMeal($tab);
        }
        else if(!($category instanceof CategoryMeal)) {
            $category = new CategoryMeal();
            $category->setStatus(1);
            $category->setRestaurant($restaurant);
            $category->setTabMeal($tab);
        }

        $form = $this->createForm(CategoryMealType::class, $category);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }

        $em = $this->getEntityManager();
        $em->persist($category);
        $em->flush();

        return $this->helper->success($category, 200);
    }

    /**
     *
     * @REST\Get("/restaurant/{id}/tab/{idTab}/categories", name="api_list_meal_categories")
     *
     */
    public function getCategoriesMealFromTab(Request $request)
    {
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $tab = $this->getTabMealRepository()->find($request->get('idTab'));

        $categories = $this->getCategoryMealRepository()->findBy(array('status' => true, 'restaurant' => $restaurant, 'tabMeal' => $tab));
        return $this->helper->success($categories, 200);
    }

    /**
     *
     * @REST\Get("/restaurant/{id}/category/{idCategory}", name="api_show_category")
     *
     */
    public function getCategory(Request $request)
    {
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $category = $this->getCategoryMealRepository()->findOneBy(array('status' => true, 'restaurant' => $restaurant, 'id' => $request->get('idCategory')));
        return $this->helper->success($category, 200);
    }

    /**
     *
     * @REST\Post("/restaurant/{id}/category/{idCategory}/position", name="api_update_category_position")
     * @REST\RequestParam(name="position")
     */
    public function updateCategoryPosition(Request $request, ParamFetcher $paramFetcher)
    {
        $params = $paramFetcher->all();
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $category = $this->getCategoryMealRepository()->findOneBy(array('status' => true, 'restaurant' => $restaurant, 'id' => $request->get('idCategory')));
        $category->setPosition($params['position']);
        $em = $this->getEntityManager();
        $em->persist($category);
        $em->flush();

        return $this->helper->success($category, 200);
    }



}