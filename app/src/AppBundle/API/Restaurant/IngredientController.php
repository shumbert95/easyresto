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
     * @REST\Put("/restaurants/{id}/ingredients/{idIngredient}/stock", name="api_ingredient_stock")
     * @REST\RequestParam(name="stock")
     */
    public function editStock(Request $request, ParamFetcher $paramFetcher)
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

        if (!$request->get('initialStock')) {
            return $this->helper->error('initialStock', true);
        } elseif (!preg_match('/\d/', $request->get('initialStock'))) {
            return $this->helper->error('param \'initialStock\' must be an integer');
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

        $params=$paramFetcher->all();
        $ingredient = $elasticaManager->getRepository('AppBundle:Ingredient')->findById($request->get('idIngredient'));
        if (!$ingredient) {
            return $this->helper->elementNotFound('Ingredient');
        }
        if($ingredient->getRestaurant() != $restaurant){
            return $this->helper->error('Ce n\'est pas un ingrédient de ce restaurant');

        }

        $ingredient->setStock($ingredient->getStock() + $params['initialStock']);
        $ingredient->setStock($ingredient->getInitialStock());
        $em = $this->getEntityManager();
        $em->persist($ingredient);
        $em->flush();

        return $this->helper->success($ingredient, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/ingredients", name="api_get_ingredient")
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

        $ingredients = $this->getIngredientRepository()->findBy(array(
            'restaurant' => $restaurant
        ));
        if(!$ingredients)
            return $this->helper->elementNotFound("Ingrédient");


        return $this->helper->success($ingredients, 200);
    }

    /**
     *
     * @REST\Put("/restaurants/{id}/ingredients", name="api_edit_ingredients")
     *
     */
    public function editIngredients(Request $request)
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
            $ingredient = $this->getIngredientRepository()->findOneBy(array('id' => $data['id']));

            if(!$ingredient){
                $return_data[] = $this->helper->elementNotFound('Ingredient id : '.$data['id']);
            }

            else {
                if($ingredient->getRestaurant() != $restaurant){
                    $return_data[] = $this->helper->error('Ce n\'est pas un ingrédient de ce restaurant');
                }
                else {
                    if (isset($data['stock'])) {
                        $stock = $ingredient->getStock() + $data['stock'];
                        $ingredient->setStock($stock);
                    }
                    if (isset($data['name'])) {
                        $name = $data['name'];
                        $ingredient->setName($name);
                    }
                    $em->persist($ingredient);
                    $em->flush();
                    $return_data[] = $ingredient;
                }
            }
        }


        return $this->helper->success($return_data, 200);
    }






}