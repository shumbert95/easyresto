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
     * @REST\Post("/restaurants/{id}/tabs/{idTab}/categories/create", name="api_create_meal_category")
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
        $category->setTab($tab);

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
     * @REST\Put("/restaurants/{id}/categories/{idCat}", name="api_edit_category")
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

        $category=$this->getContentRepository()->findOneBy(array("id" => $request->get('idCat')));


        $request_data = $request->request->all();

        if(isset($request_data['name'])){
            $category->setName($request_data['name']);
        }
        $em = $this->getEntityManager();
        $em->persist($category);

        return $this->helper->success($category, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/categories", name="api_list_categories")
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
     * @REST\Get("/restaurants/{id}/tabs/{idTab}/categories", name="api_list_meal_categories")
     *
     */
    public function getCategoriesMealFromTab(Request $request)
    {
        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $tab = $this->getTabMealRepository()->find($request->get('idTab'));

        $categories = $this->getContentRepository()->findBy(array('restaurant' => $restaurant, 'tab' => $tab,'type' => Content::TYPE_CATEGORY));
        return $this->helper->success($categories, 200);
    }





}