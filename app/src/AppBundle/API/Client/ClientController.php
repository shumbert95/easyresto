<?php

namespace AppBundle\API\Client;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Client;
use AppBundle\Entity\Restaurant;
use AppBundle\Entity\User;
use AppBundle\Form\RegistrationClientType;
use AppBundle\Form\RegistrationRestorerType;
use AppBundle\Form\RestaurantType;
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
     *
     * @REST\Post("/signup/client", name="api_client_signup")
     * @REST\RequestParam(name="email")
     * @REST\RequestParam(name="firstName")
     * @REST\RequestParam(name="lastName")
     * @REST\RequestParam(name="password")
     * @REST\RequestParam(name="type")
     * @REST\RequestParam(name="civility")
     * @REST\RequestParam(name="phoneNumber")
     * @REST\RequestParam(name="postalCode")
     */
    public function clientSignup(ParamFetcher $paramFetcher)
    {

        $params = $paramFetcher->all();

        $client = $this->getClientRepository()->findOneByEmail($params['email']);

        if ($client instanceof Client) {
            return $this->helper->error('This email is already used');
        }

        $fosUserManager = $this->get('fos_user.user_manager');

        $client = new Client();
        $form = $this->createForm(RegistrationClientType::class, $client);
        $client->setPlainPassword($params['password']);
        unset($params['password']);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }

        $client->setUsername($params['email']);
        $client->setEnabled(1);
        $client->setType(1);
        $fosUserManager->updateUser($client);

        //$this->container->get('app.mail.manager')->sendConfirmationEmailMessage($user);

        return $this->json(array(
            'newUser' => array(
                'id' => $client->getId(),
                'firstName' => $client->getFirstName(),
                'lastName' => $client->getLastName(),
                'birthDate' => $client->getBirthdate(),
                'email' => $client->getEmail(),
                'type' => $client->getType(),
            )
        ));

    }
}