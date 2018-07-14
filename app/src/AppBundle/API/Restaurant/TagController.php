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
use FOS\RestBundle\Controller\Annotations\QueryParam;

class TagController extends ApiBaseController
{
    /**
     * @param ParamFetcher $paramFetcher
     *
     * @REST\Post("/tags/create", name="api_create_tag")
     * @REST\RequestParam(name="name", nullable=true)
     */
    public function createTag(Request $request,ParamFetcher $paramFetcher)
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') &&
            !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }
        $params = $paramFetcher->all();
        $elasticaManager = $this->container->get('fos_elastica.manager');
        $tag = $elasticaManager->getRepository('AppBundle:Tag')->findByName($params['name']);

        if ($tag instanceof Tag) {
            return $this->helper->warning('Ce tag existe déjà.',403);
        }

        $tag = new Tag();
        $tag->setStatus(CategoryRestaurant::STATUS_ONLINE);

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
     * @REST\Get("/tags/{id}", name="api_show_tag")
     *
     */
    public function getTag(Request $request)
    {
        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $tag = $elasticaManager->getRepository('AppBundle:Tag')->findById($request->get('id'));
        if (!$tag) {
            return $this->helper->elementNotFound('Tag');
        }

        return $this->helper->success($tag, 200);
    }

    /**
     * @REST\Delete("/tags/{id}", name="api_delete_tag")
     */
    public function deleteTag(Request $request)
    {
        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
            return $this->helper->error('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $tag = $elasticaManager->getRepository('AppBundle:Tag')->findById($request->get('id'));
        if (!$tag) {
            return $this->helper->elementNotFound('Tag');
        }

        $em = $this->getEntityManager();
        $em->remove($tag);
        $em->flush();

        return $this->helper->success($tag, 200);
    }

    /**
     *
     * @REST\Get("/tags", name="api_list_tags")
     *
     */
    public function getTags(Request $request)
    {
        $elasticaManager = $this->container->get('fos_elastica.manager');
        $tag = $elasticaManager->getRepository('AppBundle:Tag')->findAll();
        if(!isset($tag[0]))
            $tag[]=array();

        return $this->helper->success($tag, 200);
    }

    /**
     * @QueryParam(name="name", nullable=true)
     * @REST\Get("/tags/search/count", name="api_count_tag_name")
     */
    public function getTagCount(Request $request,ParamFetcher $paramFetcher)
    {
        $params = $paramFetcher->all();

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $meals = $elasticaManager->getRepository('AppBundle:Content')->findAll();


        $name = $params['name'];
        $tags = $elasticaManager->getRepository('AppBundle:Tag')->findByNameBest($name);

        if (!$tags) {
            return $this->helper->elementNotFound('Tag');
        }

        $count = 0;
        foreach($tags as $tag){
            $count = 0;
            foreach($meals as $meal){
                $compareTags = $meal->getTags();
                foreach($compareTags as $compareTag){
                    if($tag == $compareTag){
                        $count++;
                    }
                }
            }

            $json[]=array(
                "id" => $tag->getId(),
                "name" => $tag->getName(),
                "count" => $count
            );
        }

        return $this->helper->success($json, 200);
    }
}
