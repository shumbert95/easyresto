<?php

namespace AppBundle\API;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Client;
use AppBundle\Entity\User;
use AppBundle\Form\ClientType;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ClientController extends ApiBaseController
{
    /**
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @REST\Post("/client/create", name="api_create_client")
     * @REST\RequestParam(name="address")
     * @REST\RequestParam(name="addressComplement", nullable=true)
     * @REST\RequestParam(name="city")
     * @REST\RequestParam(name="postalCode")
     * @REST\RequestParam(name="phone")
     */
    public function createClient(ParamFetcher $paramFetcher)
    {

        $client = new Client();

        $params = $paramFetcher->all();
        $form = $this->createForm(ClientType::class, $client);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }

        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        $client->setUser($user);

        $em = $this->getEntityManager();
        $em->persist($client);
        $em->flush();

        return $this->helper->success($client, 200);
    }

    /**
     * @return View
     *
     * @REST\Get("/clients", name="api_list_clients")
     *
     */
    public function getClients()
    {
        $clients = $this->getClientRepository()->findAll();
        return $this->helper->success($clients, 200);
    }
}