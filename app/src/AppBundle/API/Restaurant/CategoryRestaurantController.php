<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\CategoryRestaurant;
use AppBundle\Entity\User;
use AppBundle\Form\CategoryRestaurantType;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;

class CategoryRestaurantController extends ApiBaseController
{
    /**
     * @param ParamFetcher $paramFetcher
     *
     * @REST\Post("/categories/create", name="api_create_category_restaurant")
     * @REST\RequestParam(name="name")
     */
    public function createCategory(ParamFetcher $paramFetcher)
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
        $category = $elasticaManager->getRepository('AppBundle:CategoryRestaurant')->findByName($params['name']);

        if ($category instanceof CategoryRestaurant) {
            return $this->helper->error('This name is already used.');
        }
        $category = new CategoryRestaurant();
        $category->setStatus(CategoryRestaurant::STATUS_ONLINE);

        $form = $this->createForm(CategoryRestaurantType::class, $category);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }

        $em = $this->getEntityManager();
        $em->persist($category);
        $em->flush();

        return $this->helper->success($category, 200);
    }

    /**
     *
     * @REST\Get("/categories/{id}", name="api_show_category_restaurant")
     *
     */
    public function getCategoryRestaurant(Request $request)
    {
        if (!$request->get('id')) {
            return $this->helper->error('id', true);
        } elseif (!preg_match('/\d/', $request->get('id'))) {
            return $this->helper->error('param \'id\' must be an integer');
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $category = $elasticaManager->getRepository('AppBundle:CategoryRestaurant')->findById($request->get('id'));
        if (!$category) {
            return $this->helper->elementNotFound('Category');
        }

        return $this->helper->success($category, 200);
    }

    /**
     * @REST\Delete("/categories/{id}", name="api_delete_category_restaurant")
     */
    public function deleteCategoryRestaurant(Request $request)
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
        $category = $elasticaManager->getRepository('AppBundle:CategoryRestaurant')->findById($request->get('id'));
        if (!$category) {
            return $this->helper->elementNotFound('Category');
        }

        $em = $this->getEntityManager();
        $em->remove($category);
        $em->flush();

        return $this->helper->success($category, 200);
    }

    /**
     *
     * @REST\Get("/categories", name="api_list_restaurants_categories")
     *
     */
    public function getRestaurantsCategories()
    {
        $elasticaManager = $this->container->get('fos_elastica.manager');
        $categories = $elasticaManager->getRepository('AppBundle:CategoryRestaurant')->findAll();
        if(!isset($categories[0]))
            return $this->helper->empty();

        return $this->helper->success($categories, 200);
    }
}
