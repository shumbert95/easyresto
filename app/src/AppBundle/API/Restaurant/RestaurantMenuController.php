<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Content;
use AppBundle\Entity\Ingredient;
use AppBundle\Entity\Restaurant;
use FOS\RestBundle\Controller\Annotations as REST;
use Symfony\Component\HttpFoundation\Request;

class RestaurantMenuController extends ApiBaseController
{



    /**
     *
     * @REST\Get("/restaurants/{id}/menu", name="api_list_restaurant_menu")
     *
     */
    public function getRestaurantMenu(Request $request)
    {
        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');

        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));

        if (!$restaurant instanceof Restaurant) {
            return $this->helper->elementNotFound('Restaurant', 404);
        }

        $tabs = $elasticaManager->getRepository('AppBundle:TabMeal')->findByRestaurant($restaurant);
        //$mealSets = $elasticaManager->getRepository('AppBundle:MealSet')->findByRestaurant($restaurant);

        $json = array();
        if(isset($restaurant)) {
            if(isset($tabs)) {
                foreach ($tabs as $tab) {
                    if (isset ($tab)) {
                        if ($tab->isStatus() && $tab->getMealsIds()) {
                            $meals = json_decode($tab->getMealsIds());

                            if (is_array($meals)) {
                                foreach ($meals as $meal) {
                                    $content = $elasticaManager->getRepository('AppBundle:Content')->findById($meal);
                                    if ($content->getType() == Content::TYPE_MEAL && $content->isStatus() && $content->getRestaurant() == $restaurant) {
                                        $maxValue = -1;

                                        if ($content->getIngredients()) {
                                            foreach ($content->getIngredients() as $ingredient) {
                                                if ($ingredient->isStatus()) {
                                                    if ($maxValue == -1)
                                                        $maxValue = $ingredient->getStock();

                                                    elseif ($ingredient->getStock() < $maxValue)
                                                        $maxValue = $ingredient->getStock();
                                                }
                                            }
                                        }
                                        $arrayContent[$tab->getId()][] = array(
                                            "id" => $content->getId(),
                                            "name" => $content->getName(),
                                            "description" => $content->getDescription(),
                                            "availability" => $maxValue,
                                            "price" => $content->getPrice(),
                                            "picture" => $content->getPicture(),
                                            "tags" => $content->getTags(),
                                        );
                                    }
                                }
                            }
                        }
                        $json[] = array(
                            "id" => $tab->getId(),
                            "position" => $tab->getPosition(),
                            "name" => $tab->getName(),
                            "content" => isset($arrayContent[$tab->getId()]) ? $arrayContent[$tab->getId()] : array()
                        );
                    }

                }

            }
            /*if(isset($mealSets)){
                foreach ($mealSets as $mealSet) {
                    $maxValue = -1;
                    $mealSetElements = $elasticaManager->getRepository('AppBundle:MealSetElement')->findBySet($mealSet);
                    if(isset($mealSetElements)) {
                        $mealSetElements = $elasticaManager->getRepository('AppBundle:MealSetElement')->findBySet($mealSet);
                        foreach($mealSetElements as $mealSetElement){
                            $content = $mealSetElement->getContent();
                            if ($content->getIngredients()) {
                                foreach ($content->getIngredients() as $ingredient) {
                                    if ($ingredient->isStatus()) {
                                        if ($maxValue == -1)
                                            $maxValue = $ingredient->getStock();

                                        elseif ($ingredient->getStock() < $maxValue)
                                            $maxValue = $ingredient->getStock();
                                    }
                                }
                            }
                            $jsonMealSetElements[] = array(
                                "id" => $content->getId(),
                                "type" => $mealSetElement->getType(),
                                "name" => $content->getName(),
                                "tags" => $content->getTags(),
                            );
                        }
                        $json[] =array(
                            "id" => $mealSet->getId(),
                            "isMealSet" => true,
                            "name" => $mealSet->getName(),
                            "description" => $mealSet->getDescription(),
                            "price" => $mealSet->getPrice(),
                            "position" => $mealSet->getPosition(),
                            "availability" => $maxValue,
                            "content" => isset($jsonMealSetElements) ? $jsonMealSetElements : array()
                        );

                    }
                }
            }*/
        }
        else {
            $json[] = array();
        }


        return $this->helper->success($json, 200);
    }

}