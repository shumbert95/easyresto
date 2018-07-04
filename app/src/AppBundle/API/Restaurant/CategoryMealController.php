<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Content;
use AppBundle\Form\CategoryMealType;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
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
     * @REST\Put("/restaurant/{id}/tab/{idTab}/category/{idCat}/edit", name="api_edit_category")
     *
     */
    public function editCategory(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $tab = $this->getTabMealRepository()->findOneBy(array("id" => $request->get('idTab')));
        $category=$this->getContentRepository()->findOneBy(array("id" => $request->get('idCat'),"tab" => $tab));


        $request_data = $request->request->all();

        if($request_data['name'] != null){
            $category->setName($request_data['name']);
        }
        $em = $this->getEntityManager();
        $em->persist($category);

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



}