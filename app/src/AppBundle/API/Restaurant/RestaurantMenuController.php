<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Content;
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
        //$restaurant = $this->getRestaurantRepository()->find($request->get('id'));

        if (!$restaurant instanceof Restaurant) {
            return $this->helper->elementNotFound('Restaurant', 404);
        }

        $tabs = $elasticaManager->getRepository('AppBundle:TabMeal')->findByRestaurant($restaurant);
        //$tabs = $this->getTabMealRepository()->findBy(array("restaurant" => $restaurant));

        $json = array();
        if(isset($restaurant) && isset($tabs)) {
            foreach ($tabs as $tab) {
                if (isset ($tab)) {
                    $contents = $elasticaManager->getRepository('AppBundle:Content')->findByTab($tab);

                    foreach ($contents as $content) {
                        if ($content->getType() == Content::TYPE_CATEGORY) {
                            $arrayContent[$tab->getId()][] = array(
                                "id" => $content->getId(),
                                "position" => $content->getPosition(),
                                "type" => $content->getType(),
                                "name" => $content->getName(),
                            );
                        }
                        else {
                            $arrayContent[$tab->getId()][] = array(
                                "id" => $content->getId(),
                                "position" => $content->getPosition(),
                                "type" => $content->getType(),
                                "name" => $content->getName(),
                                "description" => $content->getDescription(),
                                "price" => $content->getPrice(),
                            );
                        }
                    }
                    $json[] = array(
                        "tab" => true,
                        "id" => $tab->getId(),
                        "position" => $tab->getPosition(),
                        "name" => $tab->getName(),
                        "content" => isset($arrayContent[$tab->getId()]) ? $arrayContent[$tab->getId()] : array()
                    );
                }
            }
            $mealSets = $elasticaManager->getRepository('AppBundle:MealSet')->findByRestaurant($restaurant);
            if(isset($mealSets)) {
                foreach ($mealSets as $mealSet) {
                    $mealSetElements = $elasticaManager->getRepository('AppBundle:MealSetElement')->findBySet($mealSet);
                    $json[] = array(
                        "tab" => false,
                        "id" => $mealSet->getId(),
                        "position" => $mealSet->getPosition(),
                        "name" => $mealSet->getName(),
                        "content" => $mealSetElements,
                    );
                }
            }

        }
        else {
            $json[] = array();
        }


        return $this->helper->success($json, 200);
    }

}