<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\CategoryMeal;
use AppBundle\Entity\Meal;
use AppBundle\Entity\Menu;
use AppBundle\Form\CategoryMealType;
use AppBundle\Form\MealType;
use AppBundle\Form\MenuType;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Request;

class RestaurantMenuController extends ApiBaseController
{



    /**
     *
     * @REST\Get("/restaurant/{id}/menu", name="api_list_restaurant_menu")
     *
     */
    public function getRestaurantMenu(Request $request)
    {
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $tabs = $this->getTabMealRepository()->findBy(array('restaurant' => $restaurant));
        $json= array();
        foreach ($tabs as $tab){
            $categories = $this->getCategoryMealRepository()->findBy(array('tabMeal'=> $tab));
            foreach($categories as $category) {
                $meals = $this->getMealRepository()->findBy(array('category'=> $category));
                foreach($meals as $meal) {
                    $mealArray[$category->getId()][] = array(
                        "id"      => $meal->getId(),
                        "name"      => $meal->getName(),
                        "price"     => $meal->getPrice(),
                        "position"     => $meal->getPosition(),
                    );
                }
                if(isset ($mealArray[$category->getId()])) {
                    $categoryArray[$tab->getId()][] = array(
                        "name" => $category->getName(),
                        "id" => $category->getId(),
                        "position" => $category->getPosition(),
                        "content" => $mealArray[$category->getId()],
                    );
                }
            }
            if(isset($categoryArray[$tab->getId()])) {
                $json[] = array(
                    "id"    => $tab->getId(),
                    "position"  => $tab->getPosition(),
                    $tab->getName() => $categoryArray[$tab->getId()]
                );
            }
        }

        return $this->helper->success($json, 200);
    }

}