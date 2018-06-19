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
     * @REST\Post("/restaurant/{id}/meal/category/create", name="api_create_meal_category")
     * @REST\RequestParam(name="name")
     */
    public function createMealCategory(Request $request, ParamFetcher $paramFetcher)
    {
        $params = $paramFetcher->all();

        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));


        $category = $this->getCategoryMealRepository()->findOneBy(array('name' => $params['name'], 'status' => true));

        if ($category instanceof CategoryMeal) {
            return $this->helper->error('This name is already used');
        }

        $category = new CategoryMeal();
        $form = $this->createForm(CategoryMealType::class, $category);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }

        $category->setStatus(1);
        $category->setRestaurant($restaurant);

        $em = $this->getEntityManager();
        $em->persist($category);
        $em->flush();

        return $this->helper->success($category, 200);
    }

    /**
     * @return View
     *
     * @REST\Get("/restaurant/{id}/meal/categories", name="api_list_meal_categories")
     *
     */
    public function getCategoriesMeal()
    {
        $categories = $this->getCategoryMealRepository()->findBy(array('status' => true));
        return $this->helper->success($categories, 200);
    }

}