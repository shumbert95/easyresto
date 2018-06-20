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
     *
     */
    public function createMeal(Request $request, ParamFetcher $paramFetcher)
    {
        $params = $paramFetcher->all();

        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $category = $this->getCategoryMealRepository()->find($request->get('idCat'));


        $meal = $this->getMealRepository()->findOneBy(array('name' => $params['name'], 'status' => true));

        if ($meal instanceof Meal && $meal->getStatus()) {
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
     * @REST\Get("/restaurant/{id}/meals", name="api_list_meals")
     *
     */
    public function getMeals()
    {
        $meals = $this->getMealRepository()->findBy(array('status' => true));
        return $this->helper->success($meals, 200);
    }



}