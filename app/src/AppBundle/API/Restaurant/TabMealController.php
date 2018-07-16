<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Restaurant;
use AppBundle\Entity\TabMeal;
use AppBundle\Form\TabMealType;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;

class TabMealController extends ApiBaseController
{
    /**
     * @param Request $request
     *
     *
     * @REST\Post("/restaurants/{id}/tabs", name="api_create_meal_tab")
     * @REST\RequestParam(name="name")
     * @REST\RequestParam(name="position")
     *
     * @return View
     */
    public function createTabMeal(Request $request, ParamFetcher $paramFetcher)
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
        $tab = new TabMeal();
        $tab->setStatus(TabMeal::STATUS_ONLINE);
        $tab->setRestaurant($restaurant);


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
     * @REST\Get("/restaurants/{id}/tabs/{idTab}", name="api_show_tab")
     *
     */
    public function getTab(Request $request)
    {
        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idTab')) {
            return $this->helper->error('id', true);
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

        if(!$tab->isStatus()){
            return $this->helper->error("Cet onglet a été supprimé");
        }

        return $this->helper->success($tab, 200);
    }

    /**
     *
     * @REST\Put("/restaurants/{id}/tabs/{idTab}/position", name="api_update_tab_position")
     * @REST\RequestParam(name="position")
     */
    public function updateTabPosition(Request $request, ParamFetcher $paramFetcher)
    {
        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idTab')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
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
        if(!$tab->isStatus()){
            return $this->helper->error("Cet onglet a été supprimé");
        }

        $tab->setPosition($params['position']);
        $em = $this->getEntityManager();
        $em->persist($tab);
        $em->flush();

        return $this->helper->success($tab, 200);
    }

    /**
     * @REST\Delete("/restaurants/{id}/tabs/{idTab}/delete", name="api_delete_tab")
     */
    public function deleteTabMeal(Request $request)
    {
        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idTab')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
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

        $tab = $elasticaManager->getRepository('AppBundle:TabMeal')->findById($request->get('idTab'));
        if(!$tab)
            return $this->helper->elementNotFound('Onglet');

        if(!$tab->isStatus()){
            return $this->helper->error("Cet onglet a été supprimé");
        }

        if($tab->getRestaurant() != $restaurant){
            return $this->helper->error('Ce n\'est pas un onglet de ce restaurant');
        }
        $em = $this->getEntityManager();
        $tab->setStatus(TabMeal::STATUS_OFFLINE);
        $tab->setMealsIds(null);
        $em->persist($tab);
        $em->flush();

        return $this->helper->success($tab, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/tabs", name="api_list_meal_tabs")
     *
     */
    public function getTabsMeal(Request $request)
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

        $tabs = $elasticaManager->getRepository('AppBundle:TabMeal')->findByRestaurant($restaurant);

        if(!isset($tabs[0]))
            $tabs=array();

        return $this->helper->success($tabs, 200);
    }

    /**
     * @param Request $request
     *
     * @REST\Put("/restaurants/{id}/tabs/{idTab}", name="api_put_tab_contents")
     */
    public function updateTabContents(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));

        if (!$restaurant instanceof Restaurant) {
            return $this->helper->elementNotFound('Restaurant');
        }
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }
        $tab = $this->getTabMealRepository()->findOneBy(array("id" => $request->get('idTab')));
        if (!$tab instanceof TabMeal) {
            return $this->helper->elementNotFound('Onglet');
        }
        if($tab->getRestaurant() != $restaurant){
            return $this->helper->warning('Ce n\'est pas un onglet de votre restaurant',403);

        }
        if(!$tab->isStatus()){
            return $this->helper->error("Cet onglet a été supprimé");
        }
        $request_data = $request->request->all();

        if (isset($request_data['contents'])) {
            $contents = json_encode($request_data['contents']);
            $tab->setMealsIds($contents);
        }
        if (isset($request_data['name'])) {
            $tab->setName($request_data['name']);
        }


        $this->getEntityManager()->persist($tab);
        $this->getEntityManager()->flush();

        return $this->helper->success($tab, 200);
    }
}
