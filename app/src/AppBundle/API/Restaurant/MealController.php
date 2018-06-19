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
     * @REST\Post("/restaurant/{id}/meal/create", name="api_create_meal")
     * @REST\RequestParam(name="name")
     * @REST\RequestParam(name="description")
     * @REST\RequestParam(name="price")
     * @REST\RequestParam(name="availability")
     * @REST\RequestParam(name="initialStock")
     * @REST\RequestParam(name="currentStock")
     * @REST\RequestParam(name="categories")
     *
     */
    public function createMeal(Request $request, ParamFetcher $paramFetcher)
    {
        $params = $paramFetcher->all();

        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));


        $meal = $this->getMealRepository()->findOneBy(array('name' => $params['name'], 'status' => true));

        if ($meal instanceof Meal) {
            return $this->helper->error('This name is already used');
        }

        $meal = new Meal();
        $form = $this->createForm(MealType::class, $meal);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }

        $meal->setStatus(1);
        $meal->setRestaurant($restaurant);

        foreach ($params['categories'] as $category)
        {
            $meal->addCategory($this->getCategoryMealRepository()->findOneBy(array('id' => $category)));

        }
        $em = $this->getEntityManager();
        $em->persist($meal);
        $em->flush();

        return $this->helper->success($meal, 200);
    }

    /**
     * @return View
     *
     * @REST\Get("/restaurant/{id}/meals", name="api_list_meals")
     *
     */
    public function getMeals()
    {
        $meals = $this->getMealRepository()->findBy(array('status' => true));
        return $this->helper->success($meals, 200);
    }



}