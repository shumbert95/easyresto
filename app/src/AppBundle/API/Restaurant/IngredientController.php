<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Content;
use AppBundle\Entity\Ingredient;
use AppBundle\Form\IngredientType;
use AppBundle\Form\MealType;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\QueryParam;

class IngredientController extends ApiBaseController
{

    /**
     * @param Request $request
     *
     *
     * @REST\Post("/restaurants/{id}/ingredients/create", name="api_create_ingredient")
     * @REST\RequestParam(name="name")
     * @REST\RequestParam(name="stock", default=0)
     *
     */
    public function createIngredient(Request $request, ParamFetcher $paramFetcher)
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

        $ingredient = new Ingredient();
        $ingredient->setStatus(Ingredient::STATUS_ONLINE);
        $ingredient->setRestaurant($restaurant);

        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }


        $em = $this->getEntityManager();
        $em->persist($ingredient);
        $em->flush();

        return $this->helper->success($ingredient, 200);
    }

    /**
     *
     * @REST\Put("/restaurants/{id}/ingredients/{idIngredient}", name="api_edit_ingredient")
     *
     */
    public function editIngredient(Request $request)
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
        $ingredient = $elasticaManager->getRepository('AppBundle:Ingredient')->findById($request->get('idIngredient'));
        if (!$ingredient) {
            return $this->helper->elementNotFound('Ingredient');
        }

        if($ingredient->getRestaurant() != $restaurant){
            return $this->helper->error('Ce n\'est pas un ingrédient de ce restaurant');
        }

        $request_data = $request->request->all();

        if(isset($request_data['name'])){
            $ingredient->setName($request_data['name']);
        }
        if(isset($request_data['stock'])){
            $ingredient->setPrice($request_data['price']);
        }

        $em = $this->getEntityManager();
        $em->persist($ingredient);

        return $this->helper->success($ingredient, 200);
    }


    /**
     *
     * @REST\Get("/restaurants/{id}/ingredients", name="api_get_ingredients")
     *
     */
    public function getIngredients(Request $request)
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

        $ingredients = $elasticaManager->getRepository('AppBundle:Ingredient')->findByRestaurant($restaurant);

        if(!$ingredients)
            return $this->helper->empty();


        return $this->helper->success($ingredients, 200);
    }

    /**
     * @QueryParam(name="name", nullable=true)
     * @REST\Get("/restaurants/{id}/ingredients/search", name="api_search_ingredient_by_name")
     */
    public function getIngredientsByName(Request $request,ParamFetcher $paramFetcher)
    {
        $params = $paramFetcher->all();

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

        $elasticaManager = $this->container->get('fos_elastica.manager');

        $name = $params['name'];
        $ingredients = $elasticaManager->getRepository('AppBundle:Ingredient')->findByNameAndRestaurantBest($restaurant, $name);

        if (!$ingredients) {
            return $this->helper->empty();
        }

        foreach($ingredients as $ingredient){
            $json[]=array(
                "id" => $ingredient->getId(),
                "name" => $ingredient->getName()
            );
        }

        return $this->helper->success($json, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/ingredients/{idIngredient}", name="api_get_ingredient")
     *
     */
    public function getIngredient(Request $request)
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

        $ingredient = $this->getIngredientRepository()->findOneBy(array(
            'restaurant' => $restaurant,
            'id'        => $request->get('idIngredient'),
        ));
        if (!$ingredient) {
            return $this->helper->elementNotFound('Ingredient');
        }
        if($ingredient->getRestaurant() != $restaurant){
            return $this->helper->error('Ce n\'est pas un ingrédient à vous');
        }
        if(!$ingredient->isStatus()){
            return $this->helper->error('Cet ingrédient a été supprimé');
        }


        return $this->helper->success($ingredient, 200);
    }

    /**
     *
     * @REST\Post("/restaurants/{id}/ingredients", name="api_update_ingredients")
     *
     */
    public function updateIngredients(Request $request)
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

        $request_data= $request->request->all();

        $return_data = array();

        $em = $this->getEntityManager();


        foreach($request_data as $data){
            $ingredient = $elasticaManager->getRepository('AppBundle:Ingredient')->findByNameAndRestaurant($data['name'],$restaurant);


            if(!$ingredient){
                $ingredient=new Ingredient();
                $ingredient->setName($data["name"]);
                $ingredient->setStatus(Ingredient::STATUS_ONLINE);
                $ingredient->setRestaurant($restaurant);
                if(isset($data['stock']))
                    $ingredient->setStock($data['stock']);
                else
                    $ingredient->setStock(0);

                $em->persist($ingredient);
                $em->flush();
                $return_data[] = $ingredient;
            }
            else {
                $ingredient->setName($data['name']);
                if (isset($data['stock'])) {
                    $stock = $ingredient->getStock() + $data['stock'];
                    $ingredient->setStock($stock);
                }
                $em->persist($ingredient);
                $em->flush();
                $return_data[] = $ingredient;
            }

        }


        return $this->helper->success($return_data, 200);
    }

    /**
     * @REST\Delete("/restaurants/{id}/ingredients/{idIngredient}", name="api_delete_ingredient")
     */
    public function deleteIngredient(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idIngredient')) {
            return $this->helper->error('idIngredient', true);
        } elseif (!preg_match('/\d/', $request->get('idIngredient'))) {
            return $this->helper->error('param \'idIngredient\' must be an integer');
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

        $ingredient = $elasticaManager->getRepository('AppBundle:Ingredient')->findById($request->get('idIngredient'));
        if (!$ingredient) {
            return $this->helper->elementNotFound('Ingredient');
        }
        if($ingredient->getRestaurant() != $restaurant){
            return $this->helper->error('Ce n\'est pas un ingrédient à vous');
        }
        /*$em = $this->getEntityManager();

        $ingredient->setStatus(Ingredient::STATUS_ONLINE);
        $em->persist($ingredient);
        $em->flush();*/

        if(!$ingredient->isStatus()){
            return $this->helper->error('Cet ingrédient a été supprimé');
        }
        $ingredient->setStatus(Ingredient::STATUS_OFFLINE);
        $ingredient->setStock(0);
        $em = $this->getEntityManager();
        $em->persist($ingredient);
        $em->flush();

        $meals = $elasticaManager->getRepository('AppBundle:Content')->findByRestaurant($restaurant);
        foreach($meals as $meal){
            $ingredients = $meal->getIngredients();

            if($ingredients->contains($ingredient)){
                $meal->removeIngredient($ingredient);
                $em->persist($meal);
                $em->flush();
            }

        }

        return $this->helper->success($ingredient, 200);
    }








}