<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Content;
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
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $tabs = $this->getTabMealRepository()->findBy(array('restaurant' => $restaurant));
        $json = array();
        if(isset($restaurant) && isset($tabs)) {
            foreach ($tabs as $tab) {
                if (isset ($tab)) {
                    $contents = $this->getContentRepository()->findBy(array('tab' => $tab,), array('position' => 'ASC'));
                    foreach ($contents as $content) {
                        if ($content->getType() == Content::TYPE_CATEGORY) {
                            $arrayContent[$tab->getId()][] = array(
                                "id" => $content->getId(),
                                "position" => $content->getPosition(),
                                "type" => $content::$types[Content::TYPE_CATEGORY],
                                "name" => $content->getName(),
                            );
                        } else {
                            $arrayContent[$tab->getId()][] = array(
                                "id" => $content->getId(),
                                "position" => $content->getPosition(),
                                "type" => $content::$types[Content::TYPE_MEAL],
                                "name" => $content->getName(),
                                "description" => $content->getDescription(),
                                "price" => $content->getPrice(),
                            );
                        }

                    }
                    $json[] = array(
                        "id" => $tab->getId(),
                        "position" => $tab->getPosition(),
                        "name" => $tab->getName(),
                        "content" => $arrayContent[$tab->getId()]
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