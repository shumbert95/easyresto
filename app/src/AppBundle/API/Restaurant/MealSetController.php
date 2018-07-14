<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Content;
use AppBundle\Entity\MealSet;
use AppBundle\Entity\MealSetElement;
use AppBundle\Form\CategoryMealType;
use AppBundle\Form\MealSetType;
use AppBundle\Form\MealType;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;

class MealSetController extends ApiBaseController
{
    /**
     * @param Request $request
     *
     *
     * @REST\Post("/restaurants/{id}/meal_sets/create", name="api_create_meal_set")
     * @REST\RequestParam(name="name", nullable=true)
     * @REST\RequestParam(name="description", nullable=true)
     * @REST\RequestParam(name="price", nullable=true)
     * @REST\RequestParam(name="position")
     *
     */
    public function createMealSet(Request $request, ParamFetcher $paramFetcher)
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


        $mealSet = new MealSet();
        $mealSet->setStatus(Content::STATUS_ONLINE);
        $mealSet->setRestaurant($restaurant);
        $em = $this->getEntityManager();


        $form = $this->createForm(MealSetType::class, $mealSet);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }

        $em->persist($mealSet);
        $em->flush();

        return $this->helper->success($mealSet, 200);
    }

    /**
     * @param Request $request
     *
     *
     * @REST\Put("/restaurants/{id}/meal_sets/{idSet}", name="api_edit_meal_set")
     * @REST\RequestParam(name="name")
     * @REST\RequestParam(name="description", nullable=true)
     * @REST\RequestParam(name="price", nullable=true)
     * @REST\RequestParam(name="position")
     *
     */
    public function editMealSet(Request $request, ParamFetcher $paramFetcher)
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

        $mealSet = $elasticaManager->getRepository('AppBundle:MealSet')->findById($request->get('idSet'));

        if(isset($params['name'])){
            $mealSet->setName($params['name']);
        }
        if(isset($params['description'])){
            $mealSet->setDescription($params['description']);
        }
        if(isset($params['price'])){
            $mealSet->setPrice($params['price']);
        }

        $em = $this->getEntityManager();

        $em->persist($mealSet);
        $em->flush();

        return $this->helper->success($mealSet, 200);
    }

    /**
     * @param Request $request
     *
     *
     * @REST\Post("/restaurants/{id}/meal_sets/{idSet}/add", name="api_add_to_meal_set")
     * @REST\RequestParam(name="meal_id", nullable=true)
     * @REST\RequestParam(name="type", nullable=true)
     *
     */
    public function addToMealSet(Request $request, ParamFetcher $paramFetcher)
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
        $meal = $elasticaManager->getRepository('AppBundle:Content')->findById($params["meal_id"]);

        if (!$meal) {
            return $this->helper->elementNotFound('$meal');
        }
        if($meal->getRestaurant() != $restaurant){
            return $this->helper->error('Ce n\'est pas un plat de ce restaurant');
        }

        if($meal->getType() == Content::TYPE_MEAL) {
            $mealSet = $elasticaManager->getRepository('AppBundle:MealSet')->findById($request->get('idSet'));
        }
        else{
            return $this->helper->error('Ce n\'est pas un plat');

        }

        $mealSetElement = $elasticaManager->getRepository('AppBundle:MealSetElement')->findBySetAndContent($mealSet,$meal);

        if (!is_object($mealSetElement)) {
            $mealSetElement = new MealSetElement();
            $mealSetElement->setContent($meal);
            $mealSetElement->setMealSet($mealSet);
            $mealSetElement->setMealSetType($params['type']);
            $em = $this->getEntityManager();
            $em->persist($mealSetElement);
            $em->flush();
        }
        else{
            return $this->helper->error('Cet élément fait déjà partie du menu');

        }

        return $this->helper->success($mealSetElement, 200);


    }

    /**
     * @param Request $request
     *
     *
     * @REST\Post("/restaurants/{id}/meal_sets/{idSet}/remove", name="api_remove_from_meal_set")
     * @REST\RequestParam(name="meal_id", nullable=true)
     *
     */
    public function removeFromMealSet(Request $request, ParamFetcher $paramFetcher)
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
        $meal = $elasticaManager->getRepository('AppBundle:Content')->findById($params["meal_id"]);

        if (!$meal) {
            return $this->helper->elementNotFound('$meal');
        }
        if($meal->getRestaurant() != $restaurant){
            return $this->helper->error('Ce n\'est pas un plat de ce restaurant');
        }

        if($meal->getType() == Content::TYPE_MEAL) {
            $mealSet = $elasticaManager->getRepository('AppBundle:MealSet')->findById($request->get('idSet'));
        }

        $mealSetElement = $elasticaManager->getRepository('AppBundle:MealSetElement')->findBySetAndContent($mealSet,$meal);

        if (is_object($mealSetElement)) {
            $em = $this->getEntityManager();
            $em->remove($mealSetElement);
            $em->flush();
        }

        return $this->helper->success($mealSet, 200);


    }

    /**
     * @param Request $request
     *
     *
     * @REST\Get("/restaurants/{id}/meal_sets/{idSet}", name="api_show_meal_set")
     *
     */
    public function showMealSet(Request $request)
    {

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));


        $mealSet = $elasticaManager->getRepository('AppBundle:MealSet')->findById($request->get('idSet'));
        $mealSetElements = $elasticaManager->getRepository('AppBundle:MealSetElement')->findBySet($mealSet);


        if(!is_object($mealSet)){
            return $this->helper->error("Id invalide");
        }
        if($mealSet->getRestaurant() != $restaurant){
            return $this->helper->error("Ce n'est pas le bon restaurant");
        }

        $json =array(
            "id" => $mealSet->getId(),
            "name" => $mealSet->getName(),
            "description" => $mealSet->getDescription(),
            "price" => $mealSet->getPrice(),
            "position" => $mealSet->getPosition(),
            "content" => isset($mealSetElements) ? $mealSetElements : array()
        );

        return $this->helper->success($json, 200);

    }

    /**
     * @param Request $request
     *
     *
     * @REST\Get("/restaurants/{id}/meal_sets", name="api_show_meal_sets")
     *
     */
    public function showMealSets(Request $request)
    {

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));

        $mealSets = $elasticaManager->getRepository('AppBundle:MealSet')->findByRestaurant($restaurant);

        $json=array();
        foreach ($mealSets as $mealSet){
            $mealSetElements = $elasticaManager->getRepository('AppBundle:MealSetElement')->findBySet($mealSet);
            $json["mealSets"][] =array(
                "id" => $mealSet->getId(),
                "name" => $mealSet->getName(),
                "description" => $mealSet->getDescription(),
                "price" => $mealSet->getPrice(),
                "position" => $mealSet->getPosition(),
                "content" => isset($mealSetElements) ? $mealSetElements : array()
            );
        }

        return $this->helper->success($json, 200);

    }
}