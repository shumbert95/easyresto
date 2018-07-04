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
        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $params = $paramFetcher->all();
        $content = $this->getContentRepository()->findOneBy(
            array(
                'restaurant' => $restaurant,
                'id' => $request->get('idContent')
            ));

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
        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }
        $content = $this->getContentRepository()->findOneBy(
            array(
                'restaurant' => $restaurant,
                'id' => $request->get('idContent')
            ));

        $em = $this->getEntityManager();
        $em->remove($content);
        $em->flush();

        return $this->helper->success($content, 200);
    }
}