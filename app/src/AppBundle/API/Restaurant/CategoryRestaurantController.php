<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\CategoryRestaurant;
use AppBundle\Form\CategoryRestaurantType;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;

class CategoryRestaurantController extends ApiBaseController
{
    /**
     * @param ParamFetcher $paramFetcher
     *
     * @REST\Post("/restaurants/category/create", name="api_create_category_restaurant")
     * @REST\RequestParam(name="name")
     */
    public function createCategory(ParamFetcher $paramFetcher)
    {
        $params = $paramFetcher->all();
        if (!$params['name']) {
            return $this->helper->error('name', true);
        }

        $elasticaManager = $this->container->get('fos_elastica.manager');
        $category = $elasticaManager->getRepository('AppBundle:CategoryRestaurant')->findByName($params['name']);

        if ($category instanceof CategoryRestaurant && $category->getStatus()) {
            return $this->helper->error('This name is already used.');
        }
        else if (($category instanceof CategoryRestaurant && !$category->getStatus())) {
            $category->setStatus(1);
        }
        else if(!($category instanceof CategoryRestaurant)) {
            $category = new CategoryRestaurant();
            $category->setStatus(1);
        }

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
     * @REST\Get("/restaurants/category/{id}/", name="api_show_category_restaurant")
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
     * @REST\Delete("/restaurants/category/{id}", name="api_delete_category_restaurant")
     */
    public function deleteCategoryRestaurant(Request $request)
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

        $em = $this->getEntityManager();
        $em->remove($category);
        $em->flush();

        return $this->helper->success($category, 200);
    }

    /**
     *
     * @REST\Get("/restaurants/categories", name="api_list_restaurants_categories")
     *
     */
    public function getRestaurantsCategories()
    {
        $elasticaManager = $this->container->get('fos_elastica.manager');
        $categories = $elasticaManager->getRepository('AppBundle:CategoryRestaurant')->findAll();

        return $this->helper->success($categories, 200);
    }
}
