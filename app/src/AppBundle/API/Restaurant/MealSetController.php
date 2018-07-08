<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Content;
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
     * @REST\RequestParam(name="name")
     * @REST\RequestParam(name="description", nullable=true)
     * @REST\RequestParam(name="price", nullable=true)
     * @REST\RequestParam(name="meals", nullable=true)
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


        $mealSet = new Content();
        $mealSet->setStatus(Content::STATUS_ONLINE);
        $mealSet->setType(Content::TYPE_MEALSET);
        $mealSet->setRestaurant($restaurant);
        $em = $this->getEntityManager();


        unset($params['meals']);
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
        if($meal->getType() == Content::TYPE_MEAL) {
            $mealSet = $elasticaManager->getRepository('AppBundle:Content')->findById($request->get('idSet'));
            $mealSetElementCheck = $elasticaManager->getRepository('AppBundle:Content')->findIfExists($mealSet,$meal);

            if (!$mealSetElementCheck) {

                $mealSetElement = new MealSetElement();
                $mealSetElement->setContent($meal);
                $mealSetElement->setMealSetType($params['type']);
                $mealSet->addMealSetElement($mealSetElement);
                $em = $this->getEntityManager();
                $em->persist($mealSetElement);
                $em->flush();
            }
        }

        return $this->helper->success($mealSet, 200);


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
        $mealSet = $elasticaManager->getRepository('AppBundle:Content')->findById($request->get('idSet'));
        $mealSetElementCheck = $elasticaManager->getRepository('AppBundle:Content')->findIfExists($mealSet,$meal);

        if($mealSetElementCheck) {
            $mealSetElements = $this->getMealSetElementRepository()->findBy(array("content" => $meal));
            foreach($mealSetElements as $mealSetElement) {
                $mealSetContains = $mealSet->getMealSetElements();
                if($mealSetContains->contains($mealSetElement)) {
                    $mealSet->removeMealSetElement($mealSetElement);
                    $em = $this->getEntityManager();
                    $em->remove($mealSetElement);
                    $em->flush();
                }
            }
        }
        else{
            return $this->helper->error('Il n\'y a rien à retirer');

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


        $mealSet = $elasticaManager->getRepository('AppBundle:Content')->findById($request->get('idSet'));


        return $this->helper->success($mealSet, 200);


    }
}