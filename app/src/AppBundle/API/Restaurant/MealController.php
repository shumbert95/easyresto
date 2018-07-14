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
use Doctrine\Common\Collections\ArrayCollection;
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
     * @REST\Post("/restaurants/{id}/meals/create", name="api_create_meal")
     * @REST\RequestParam(name="name", nullable=true)
     * @REST\RequestParam(name="description", nullable=true)
     * @REST\RequestParam(name="price", nullable=true)
     * @REST\RequestParam(name="availability", nullable=true)
     * @REST\RequestParam(name="position", nullable=true)
     * @REST\RequestParam(name="picture", nullable=true)
     * @REST\RequestParam(name="tags", nullable=true)
     * @REST\RequestParam(name="ingredients", nullable=true)
     *
     */
    public function createMeal(Request $request, ParamFetcher $paramFetcher)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

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

        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $params = $paramFetcher->all();


        $meal = new Content();
        $meal->setStatus(Content::STATUS_ONLINE);
        $meal->setType(Content::TYPE_MEAL);
        $meal->setRestaurant($restaurant);


        if(isset($params['tags'])){
            $tags = $params['tags'];
            $arrayTags = new ArrayCollection();
            foreach($tags as $tagId){
                $tag = $elasticaManager->getRepository('AppBundle:Tag')->findById($tagId["id"]);
                if($tag && !$arrayTags->contains($tag)){
                    $arrayTags->add($tag);
                }
            }
            $meal->setTags($arrayTags);
        }
        if(isset($params['ingredients'])){
            $ingredients = $params['ingredients'];
            $arrayIngredients = new ArrayCollection();
            foreach($ingredients as $ingredientId){
                $ingredient = $elasticaManager->getRepository('AppBundle:Ingredient')->findById($ingredientId["id"]);
                if($ingredient && !$arrayIngredients->contains($ingredient) && $ingredient->getRestaurant() == $restaurant){
                    $arrayIngredients->add($ingredient);
                }
            }
            $meal->setIngredients($arrayIngredients);
        }

        unset($params['tags']);
        unset($params['ingredients']);

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
     * @REST\Put("/restaurants/{id}/meals/{idMeal}", name="api_edit_meal")
     *
     */
    public function editMeal(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idMeal')) {
            return $this->helper->error('idMeal', true);
        } elseif (!preg_match('/\d/', $request->get('idMeal'))) {
            return $this->helper->error('param \'idMeal\' must be an integer');
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

        $meal = $elasticaManager->getRepository('AppBundle:Content')->findById($request->get('idMeal'));
        if (!$meal) {
            return $this->helper->elementNotFound('Meal');
        }
        if($meal->getType() != Content::TYPE_MEAL){
            return $this->helper->error('Il ne s\'agit pas d\'un plat.');
        }
        if($meal->getRestaurant() != $restaurant){
            return $this->helper->error('Ce n\'est pas un plat de ce restaurant');
        }

        $request_data = $request->request->all();

        if(isset($request_data['name'])){
            $meal->setName($request_data['name']);
        }
        if(isset($request_data['price'])){
            $meal->setPrice($request_data['price']);
        }
        if(isset($request_data['description'])){
            $meal->setDescription($request_data['description']);
        }
        if(isset($request_data['picture'])){
            $meal->setPicture($request_data['picture']);
        }
        if(isset($request_data['tags'])){
            $tags = $request_data['tags'];
            $arrayTags = new ArrayCollection();
            foreach($tags as $tagId){
                $tag = $elasticaManager->getRepository('AppBundle:Tag')->findById($tagId["id"]);
                if($tag && !$arrayTags->contains($tag)){
                    $arrayTags->add($tag);
                }
            }
            $meal->setTags($arrayTags);
        }
        if(isset($request_data['ingredients'])){
            $ingredients = $request_data['ingredients'];
            $arrayIngredients = new ArrayCollection();
            foreach($ingredients as $ingredientId){
                $ingredient = $elasticaManager->getRepository('AppBundle:Ingredient')->findById($ingredientId["id"]);
                if($ingredient && !$arrayIngredients->contains($ingredient) && $ingredient->getRestaurant() == $restaurant){
                    $arrayIngredients->add($ingredient);
                }
            }
            $meal->setIngredients($arrayIngredients);
        }

        $em = $this->getEntityManager();
        $em->persist($meal);
        $em->flush();

        return $this->helper->success($meal, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/meals/{idMeal}", name="api_show_meal")
     *
     */
    public function getMeal(Request $request)
    {
        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idMeal')) {
            return $this->helper->error('idMeal', true);
        } elseif (!preg_match('/\d/', $request->get('idMeal'))) {
            return $this->helper->error('param \'idMeal\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));
        if (!$restaurant) {
            return $this->helper->elementNotFound('Restaurant');
        }

        $meal = $elasticaManager->getRepository('AppBundle:Content')->findById($request->get('idMeal'));
        if (!$meal) {
            return $this->helper->elementNotFound('Meal');
        }
        if($meal->getRestaurant() != $restaurant){
            return $this->helper->error('Ce n\'est pas un plat de ce restaurant');
        }
        if($meal->getType() != Content::TYPE_MEAL){
            return $this->helper->error('Ce n\'est pas un plat.');
        }

        return $this->helper->success($meal, 200);
    }
  
    /**
     * @REST\Delete("/restaurants/{id}/meals/{idMeal}", name="api_delete_meal")
     */
    public function deleteMeal(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idMeal')) {
            return $this->helper->error('idMeal', true);
        } elseif (!preg_match('/\d/', $request->get('idMeal'))) {
            return $this->helper->error('param \'idMeal\' must be an integer');
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

        $meal = $elasticaManager->getRepository('AppBundle:Content')->findById($request->get('idMeal'));
        if (!$meal) {
            return $this->helper->elementNotFound('Meal');
        }
        if($meal->getRestaurant() != $restaurant){
            return $this->helper->error('Ce n\'est pas un plat de ce restaurant');
        }

        $em = $this->getEntityManager();
        $em->remove($meal);
        $em->flush();

        return $this->helper->success($meal, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/meals", name="api_list_meals")
     *
     */
    public function getMeals(Request $request)
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

        $meals = $elasticaManager->getRepository('AppBundle:Content')->findByRestaurant($restaurant, Content::TYPE_MEAL);

        if(!isset($meals[0])){
            $meals=array();
        }

        return $this->helper->success($meals, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/tabs/{idTab}/meals", name="api_list_meals_by_tab")
     *
     */
    public function getMealsFromTab(Request $request)
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


        $meals = $elasticaManager->getRepository('AppBundle:Content')->findByTab($tab, Content::TYPE_MEAL);

        if(!isset($meals[0])){
            $meals=array();
        }

        return $this->helper->success($meals, 200);
    }


}