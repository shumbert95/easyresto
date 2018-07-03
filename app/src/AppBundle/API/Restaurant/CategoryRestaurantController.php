<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\CategoryRestaurant;
use AppBundle\Entity\Restaurant;
use AppBundle\Entity\User;
use AppBundle\Form\CategoryRestaurantType;
use AppBundle\Form\RegistrationClientType;
use AppBundle\Form\RestaurantType;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

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


        $category = $this->getCategoryRestaurantRepository()->findOneBy(array('name' => $params['name']));

        if ($category instanceof CategoryRestaurant && $category->getStatus()) {
            return $this->helper->error('This name is already used');
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
        $category = $this->getCategoryRestaurantRepository()->findOneBy(array('status' => true, 'id' => $request->get('id')));
        return $this->helper->success($category, 200);
    }

    /**
     * @REST\Delete("/restaurants/category/{id}", name="api_delete_category_restaurant")
     */
    public function deleteCategoryRestaurant(Request $request)
    {
        $category = $this->getCategoryRestaurantRepository()->findOneBy(
            array(
                'status' => true,
                'id' => $request->get('id')
            ));

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
        $restaurants = $this->getCategoryRestaurantRepository()->findBy(array('status' => true));
        return $this->helper->success($restaurants, 200);
    }


}