<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\CategoryRestaurant;
use AppBundle\Entity\Moment;
use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use AppBundle\Form\CategoryRestaurantType;
use AppBundle\Form\MomentType;
use AppBundle\Form\TagType;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;

class TagController extends ApiBaseController
{
    /**
     * @param ParamFetcher $paramFetcher
     *
     * @REST\Post("/restaurants/{id}/tags/create", name="api_create_tag")
     * @REST\RequestParam(name="name", nullable=true)
     */
    public function createTag(Request $request,ParamFetcher $paramFetcher)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        if(!$restaurant){
            return $this->helper->elementNotFound('Restaurant');

        }
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }
        $params = $paramFetcher->all();
        $elasticaManager = $this->container->get('fos_elastica.manager');
        $tag = new Tag();
        $tag->setStatus(CategoryRestaurant::STATUS_ONLINE);
        $tag->setRestaurant($restaurant);

        $form = $this->createForm(TagType::class, $tag);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }


        $em = $this->getEntityManager();
        $em->persist($tag);
        $em->flush();

        return $this->helper->success($tag, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/tags/{idTag}", name="api_show_moment")
     *
     */
    public function getMoment(Request $request)
    {
        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $moment = $elasticaManager->getRepository('AppBundle:Moment')->findById($request->get('id'));
        if (!$moment) {
            return $this->helper->elementNotFound('Moment');
        }

        return $this->helper->success($moment, 200);
    }

    /**
     * @REST\Delete("/restaurants/{id}/tags/{idTag}", name="api_delete_moment")
     */
    public function deleteMoment(Request $request)
    {

        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        if(!$restaurant){
            return $this->helper->elementNotFound('Restaurant');

        }
        $restaurantUsers = $restaurant->getUsers();

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$restaurantUsers->contains($user)){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $moment = $elasticaManager->getRepository('AppBundle:Moment')->findById($request->get('id'));
        if (!$moment) {
            return $this->helper->elementNotFound('Moment');
        }

        $em = $this->getEntityManager();
        $em->remove($moment);
        $em->flush();

        return $this->helper->success($moment, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/{id}/tags", name="api_list_moments")
     *
     */
    public function getMoments(Request $request)
    {
        $restaurant = $this->getRestaurantRepository()->findOneBy(array("id" => $request->get('id')));
        if(!$restaurant){
            return $this->helper->elementNotFound('Restaurant');

        }
        $elasticaManager = $this->container->get('fos_elastica.manager');
        $moments = $elasticaManager->getRepository('AppBundle:Tag')->findAllByRestaurant($restaurant);
        if(!isset($moments[0]))
            $moments[]=array();

        return $this->helper->success($moments, 200);
    }
}
