<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Menu;
use AppBundle\Form\MenuType;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;

class MenuController extends ApiBaseController
{
    /**
     * @param Request $request
     *
     *
     * @REST\Post("/restaurant/{id}/menu/create", name="api_create_menu")
     * @REST\RequestParam(name="name")
     * @REST\RequestParam(name="description")
     * @REST\RequestParam(name="price")
     * @REST\RequestParam(name="availability")
     * @REST\RequestParam(name="meals")
     */
    public function createMenu(Request $request, ParamFetcher $paramFetcher)
    {
        $params = $paramFetcher->all();

        $restaurant = $this->getRestaurantRepository()->find($request->get('id'));


        $menu = $this->getMenuRepository()->findOneBy(array('name' => $params['name'], 'status' => true));

        if ($menu instanceof Menu) {
            return $this->helper->error('This name is already used');
        }

        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }

        $menu->setStatus(1);
        $menu->setRestaurant($restaurant);
        foreach ($params['meals'] as $meal)
        {
            $menu->addMeal($this->getContentRepository()->findOneBy(array('id' => $meal)));

        }

        $em = $this->getEntityManager();
        $em->persist($menu);
        $em->flush();

        return $this->helper->success($menu, 200);
    }

    /**
     *
     * @REST\Get("/restaurant/{id}/menus", name="api_list_menus")
     *
     */
    public function getMenus()
    {
        $meals = $this->getMenuRepository()->findBy(array('status' => true));
        return $this->helper->success($meals, 200);
    }
}