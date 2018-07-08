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
     * @REST\Post("/restaurants/{id}/tabs/{idTab}/categories/create", name="api_create_meal_category")
     * @REST\RequestParam(name="name", nullable=true)
     * @REST\RequestParam(name="position", nullable=true)
     */
    public function createMealCategory(Request $request, ParamFetcher $paramFetcher)
    {
        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idTab')) {
            return $this->helper->error('idTab', true);
        } elseif (!preg_match('/\d/', $request->get('idTab'))) {
            return $this->helper->error('param \'idTab\' must be an integer');
        }

        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));
        if (!$restaurant) {
            return $this->helper->elementNotFound('Restaurant');
        }

        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $params = $paramFetcher->all();
        $tab = $elasticaManager->getRepository('AppBundle:TabMeal')->findById($request->get('idTab'));
        if (!$tab) {
            return $this->helper->elementNotFound('TabMeal');
        }
        if($tab->getRestaurant() != $restaurant){
            return $this->helper->error('Ce n\'est pas un onglet de ce restaurant');
        }


        $category = new Content();
        $category->setStatus(Content::STATUS_ONLINE);
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

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idCat')) {
            return $this->helper->error('idCat', true);
        } elseif (!preg_match('/\d/', $request->get('idCat'))) {
            return $this->helper->error('param \'idCat\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));
        if (!$restaurant) {
            return $this->helper->elementNotFound('Restaurant');
        }

        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $category = $elasticaManager->getRepository('AppBundle:Content')->findById($request->get('idCat'));

        if($category->getType() != Content::TYPE_CATEGORY){
            return $this->helper->error('Il ne s\'agit pas d\'une catégorie.');
        }

        if($category->getRestaurant() != $restaurant){
            return $this->helper->error('Ce n\'est pas une catégorie de ce restaurant');
        }

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
        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));
        if (!$restaurant) {
            return $this->helper->elementNotFound('Restaurant');
        }

        $categories = $elasticaManager->getRepository('AppBundle:Content')->findByRestaurant($restaurant, Content::TYPE_CATEGORY);

        return $this->helper->success($categories, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/tabs/{idTab}/categories", name="api_list_meal_categories")
     *
     */
    public function getCategoriesMealFromTab(Request $request)
    {
        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idTab')) {
            return $this->helper->error('idTab', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'idTab\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));
        if (!$restaurant) {
            return $this->helper->elementNotFound('Restaurant');
        }

        $tab = $elasticaManager->getRepository('AppBundle:TabMeal')->findById($request->get('idTab'));
        if (!$tab) {
            return $this->helper->elementNotFound('TabMeal');
        }
        if($tab->getRestaurant() != $restaurant){
            return $this->helper->error('Ce n\'est pas un onglet de ce restaurant');
        }

        $categories = $elasticaManager->getRepository('AppBundle:Content')->findByTab($tab, Content::TYPE_CATEGORY);

        $categories = $this->getContentRepository()->findBy(array('restaurant' => $restaurant, 'tab' => $tab,'type' => Content::TYPE_CATEGORY));
        return $this->helper->success($categories, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/categories/{idCategory}", name="api_show_category")
     *
     */
    public function getCategory(Request $request)
    {
        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idCategory')) {
            return $this->helper->error('idCategory', true);
        } elseif (!preg_match('/\d/', $request->get('idCategory'))) {
            return $this->helper->error('param \'idCategory\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));
        if (!$restaurant) {
            return $this->helper->elementNotFound('Restaurant');
        }

        $category = $elasticaManager->getRepository('AppBundle:Content')->findById($request->get('idCategory'));
        if (!$category) {
            return $this->helper->elementNotFound('CategoryMeal');
        }
        if($category->getRestaurant() != $restaurant){
            return $this->helper->error('Ce n\'est pas une catégorie de ce restaurant');
        }

        return $this->helper->success($category, 200);
    }
}
