<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\CategoryRestaurant;
use AppBundle\Entity\Moment;
use AppBundle\Entity\User;
use AppBundle\Form\CategoryRestaurantType;
use AppBundle\Form\MomentType;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;

class MomentController extends ApiBaseController
{
    /**
     * @param ParamFetcher $paramFetcher
     *
     * @REST\Post("/moments", name="api_create_moment")
     * @REST\RequestParam(name="name")
     * @REST\RequestParam(name="moment")
     */
    public function createMoment(ParamFetcher $paramFetcher)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if( !($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN'))) {
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $params = $paramFetcher->all();
        if (!$params['name']) {
            return $this->helper->error('name', true);
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $moment = $elasticaManager->getRepository('AppBundle:Moment')->findByName($params['name']);

        if ($moment instanceof Moment) {
            return $this->helper->warning('Nom déjà utilisé',400);
        }
        $moment = new Moment();
        $moment->setStatus(Moment::STATUS_ONLINE);

        $form = $this->createForm(MomentType::class, $moment);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }

        $em = $this->getEntityManager();
        $em->persist($moment);
        $em->flush();

        return $this->helper->success($moment, 200);
    }

    /**
     *
     * @REST\Get("/moments/{id}", name="api_show_moment")
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
     * @REST\Delete("/moments/{id}", name="api_delete_moment")
     */
    public function deleteMoment(Request $request)
    {

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

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

        $em = $this->getEntityManager();
        $em->remove($moment);
        $em->flush();

        return $this->helper->success($moment, 200);
    }

    /**
     *
     * @REST\Get("/moments", name="api_list_moments")
     *
     */
    public function getMoments()
    {
        $elasticaManager = $this->container->get('fos_elastica.manager');
        $moments = $elasticaManager->getRepository('AppBundle:Moment')->findAll();
        if(!isset($moments[0]))
            return $this->helper->empty();

        return $this->helper->success($moments, 200);
    }
}
