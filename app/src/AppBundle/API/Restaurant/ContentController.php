<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;

class ContentController extends ApiBaseController
{
    /**
     *
     * @REST\Put("/restaurants/{id}/contents/{idContent}/position", name="api_update_content_position")
     * @REST\RequestParam(name="position")
     */
    public function updateContentPosition(Request $request, ParamFetcher $paramFetcher)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idContent')) {
            return $this->helper->error('idContent', true);
        } elseif (!preg_match('/\d/', $request->get('idContent'))) {
            return $this->helper->error('param \'idContent\' must be an integer');
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

        $content = $elasticaManager->getRepository('AppBundle:Content')->findById($request->get('idContent'));
        if (!$content) {
            return $this->helper->elementNotFound('Content');
        }

        if($content->getRestaurant() != $restaurant){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $content->setPosition($params['position']);
        $em = $this->getEntityManager();
        $em->persist($content);
        $em->flush();

        return $this->helper->success($content, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/tabs/{idTab}/contents", name="api_list_contents_by_tab")
     *
     */
    public function getContentsFromTab(Request $request)
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
        if (!$restaurant) {
            return $this->helper->elementNotFound('Tab');
        }

        $contents = $elasticaManager->getRepository('AppBundle:Content')->findByTab($tab);


        return $this->helper->success($contents, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/contents/{idContent}", name="api_show_content")
     *
     */
    public function getContent(Request $request)
    {

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idContent')) {
            return $this->helper->error('idMeal', true);
        } elseif (!preg_match('/\d/', $request->get('idContent'))) {
            return $this->helper->error('param \'idContent\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $restaurant = $elasticaManager->getRepository('AppBundle:Restaurant')->findById($request->get('id'));
        if (!$restaurant) {
            return $this->helper->elementNotFound('Restaurant');
        }

        $content = $elasticaManager->getRepository('AppBundle:Content')->findById($request->get('idContent'));
        if (!$content) {
            return $this->helper->elementNotFound('Meal');
        }
        if($content->getRestaurant() != $restaurant){
            return $this->helper->error('Ce n\'est pas un plat de ce restaurant');
        }

        return $this->helper->success($content, 200);
    }

    /**
     * @REST\Delete("/restaurants/{id}/contents/{idContent}", name="api_delete_content")
     */
    public function deleteContent(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if (!$request->get('idContent')) {
            return $this->helper->error('idContent', true);
        } elseif (!preg_match('/\d/', $request->get('idContent'))) {
            return $this->helper->error('param \'idContent\' must be an integer');
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

        $content = $elasticaManager->getRepository('AppBundle:Content')->findById($request->get('idContent'));
        if (!$content) {
            return $this->helper->elementNotFound('Content');
        }
        if($content->getRestaurant() != $restaurant){
            return $this->helper->error('Ce n\'est pas un contenu à vous');
        }

        $em = $this->getEntityManager();
        $em->remove($content);
        $em->flush();

        return $this->helper->success($content, 200);
    }
}