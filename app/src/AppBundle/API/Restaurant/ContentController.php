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
     * @REST\Put("/restaurant/{id}/content/{idContent}/position", name="api_update_content_position")
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

        $content->setPosition($params['position']);
        $em = $this->getEntityManager();
        $em->persist($content);
        $em->flush();

        return $this->helper->success($content, 200);
    }

    /**
     * @REST\Delete("/restaurant/{id}/content/{idContent}", name="api_delete_content")
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

        $em = $this->getEntityManager();
        $em->remove($content);
        $em->flush();

        return $this->helper->success($content, 200);
    }
}