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

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }
        $params = $paramFetcher->all();
        $tab = $this->getTabMealRepository()->find($request->get('idTab'));


        $category = new Content();
        $category->setStatus(1);
        $category->setType(Content::TYPE_CATEGORY);
        $category->setRestaurant($restaurant);
        $category->setTabMeal($tab);

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
     * @REST\Get("/restaurant/{id}/categories", name="api_list_categories")
     *
     */
    public function getCategories(Request $request)
    {
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $categories = $this->getContentRepository()->findBy(array('restaurant' => $restaurant,'type' => Content::TYPE_CATEGORY));
        return $this->helper->success($categories, 200);
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

        $categories = $this->getContentRepository()->findBy(array('status' => true, 'restaurant' => $restaurant, 'tabMeal' => $tab,'type' => Content::TYPE_CATEGORY));
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
        $category = $this->getContentRepository()->findOneBy(array('status' => true, 'restaurant' => $restaurant, 'id' => $request->get('idCategory')));
        return $this->helper->success($category, 200);
    }

    /**
     *
     * @REST\Post("/restaurant/{id}/category/{idCategory}/position", name="api_update_category_position")
     * @REST\RequestParam(name="position")
     */
    public function updateCategoryPosition(Request $request, ParamFetcher $paramFetcher)
    {

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }
        $params = $paramFetcher->all();
        $category = $this->getContentRepository()->findOneBy(array('status' => true, 'restaurant' => $restaurant, 'id' => $request->get('idCategory')));
        $category->setPosition($params['position']);
        $em = $this->getEntityManager();
        $em->persist($category);
        $em->flush();

        return $this->helper->success($category, 200);
    }

    /**
     * @REST\Delete("/restaurant/{id}/category/{idCategory}", name="api_delete_category_meal")
     */
    public function deleteCategoryMeal(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }
        $category = $this->getContentRepository()->findOneBy(
            array(
                'status' => true,
                'restaurant' => $restaurant,
                'id' => $request->get('idCategory')
            ));

        $em = $this->getEntityManager();
        $em->remove($category);
        $em->flush();

        return $this->helper->success($category, 200);
    }



}