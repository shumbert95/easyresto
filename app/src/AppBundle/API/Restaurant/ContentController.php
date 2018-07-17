<?php

namespace AppBundle\API\Restaurant;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Content;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;

class ContentController extends ApiBaseController
{
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
        if(!$content->isStatus()){
            return $this->helper->error('Ce plat a été supprimé');
        }

        return $this->helper->success($content, 200);
    }
}