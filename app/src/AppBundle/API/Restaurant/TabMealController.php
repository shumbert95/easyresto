<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\TabMeal;
use AppBundle\Form\TabMealType;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;

class TabMealController extends ApiBaseController
{
    /**
     * @param Request $request
     *
     *
     * @REST\Post("/restaurant/{id}/tab/create", name="api_create_meal_tab")
     * @REST\RequestParam(name="name")
     * @REST\RequestParam(name="position")
     */
    public function createTabMeal(Request $request, ParamFetcher $paramFetcher)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $params = $paramFetcher->all();
        $tab = $this->getTabMealRepository()->findOneBy(array('name' => $params['name']));

        if ($tab instanceof TabMeal && $tab->getStatus()) {
            return $this->helper->error('This name is already used');
        }
        else if (($tab instanceof TabMeal && !$tab->getStatus())) {
            $tab->setStatus(1);
        }
        else if(!($tab instanceof TabMeal)) {
            $tab = new TabMeal();
            $tab->setStatus(1);
            $tab->setRestaurant($restaurant);
        }

        $form = $this->createForm(TabMealType::class, $tab);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }

        $em = $this->getEntityManager();
        $em->persist($tab);
        $em->flush();

        return $this->helper->success($tab, 200);
    }

    /**
     *
     * @REST\Get("/restaurant/{id}/tab/{idTab}", name="api_show_tab")
     *
     */
    public function getTab(Request $request)
    {
        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));
        $tab = $this->getTabMealRepository()->findOneBy(array('status' => true, 'restaurant' => $restaurant, 'id' => $request->get('idTab')));
        return $this->helper->success($tab, 200);
    }

    /**
     *
     * @REST\Post("/restaurant/{id}/tab/{idTab}/position", name="api_update_tab_position")
     * @REST\RequestParam(name="position")
     */
    public function updateTabPosition(Request $request, ParamFetcher $paramFetcher)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $params = $paramFetcher->all();
        $tab = $this->getTabMealRepository()->findOneBy(array('status' => true, 'restaurant' => $restaurant, 'id' => $request->get('idTab')));
        $tab->setPosition($params['position']);
        $em = $this->getEntityManager();
        $em->persist($tab);
        $em->flush();

        return $this->helper->success($tab, 200);
    }

    /**
     * @REST\Delete("/restaurant/{id}/tab/{idTab}/delete", name="api_delete_tab")
     */
    public function deleteTabMeal(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $tab = $this->getTabMealRepository()->findOneBy(
            array(
                'status' => true,
                'restaurant' => $restaurant,
                'id' => $request->get('idTab')
            ));

        $em = $this->getEntityManager();
        $em->remove($tab);
        $em->flush();

        return $this->helper->success($tab, 200);
    }

    /**
     *
     * @REST\Get("/restaurant/{id}/tabs", name="api_list_meal_tabs")
     *
     */
    public function getTabsMeal()
    {
        $tabs = $this->getTabMealRepository()->findBy(array('status' => true));
        return $this->helper->success($tabs, 200);
    }

}