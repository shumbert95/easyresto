<?php

namespace AppBundle\API;

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

class UserController extends ApiBaseController
{

    /**
     * @param ParamFetcher $paramFetcher
     *
     *
     * @REST\Post("/user/create", name="api_create_user")
     * @REST\RequestParam(name="email")
     * @REST\RequestParam(name="firstName")
     * @REST\RequestParam(name="lastName")
     * @REST\RequestParam(name="password")
     * @REST\RequestParam(name="type")
     * @REST\RequestParam(name="civility")
     * @REST\RequestParam(name="phoneNumber")
     * @REST\RequestParam(name="postalCode")
     */
    public function createUser(ParamFetcher $paramFetcher) {

        $params = $paramFetcher->all();

        $user = $this->getUserRepository()->findOneByEmail($params['email']);

        if ($user instanceof User) {
            return $this->helper->error('This email is already used');
        }

        $fosUserManager = $this->get('fos_user.user_manager');

        $user = new User();
        $form = $this->createForm(RegistrationClientType::class, $user);
        $user->setPlainPassword($params['password']);
        unset($params['password']);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }

        $user->setUsername($params['email']);
        $user->setEnabled(1);
        $fosUserManager->updateUser($user);

        //$this->container->get('app.mail.manager')->sendConfirmationEmailMessage($user);

        return $this->json(array(
            'newUser' => array(
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'birthDate' => $user->getBirthdate(),
                'email' => $user->getEmail(),
                'type' => $user->getType(),
            )
        ));

    }

    /**
     *
     * @REST\Get("/user/{id}", name="api_detail_user")
     *
     */
    public function getUserById(Request $request)
    {
        $user = $this->getUserRepository()->find($request->get('id'));
        return $this->helper->success($user, 200);
    }

    /**
     *
     * @REST\Get("/user", name="api_detail_logged_user")
     *
     */
    public function getLoggedUser()
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        return $this->helper->success($user, 200);
    }


    //TODO: Faire marcher l'edit
    /**
     *
     * @REST\Put("/user/edit", name="api_edit_logged_user")
     *
     */
    public function editUser(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $form = $this->createForm(RegistrationRestorerType::class, $user);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }


        //$fosUserManager->updateUser($user);

        return $this->json(array(
            'newUser' => array(
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'birthDate' => $user->getBirthdate(),
                'email' => $user->getEmail(),
                'type' => $user->getType(),
                'test' => $form->getData(),
            )
        ));
    }



    /**
     * @return View
     *
     * @REST\Get("/users", name="api_list_users")
     *
     */
    public function getUsers()
    {
        $users = $this->getUserRepository()->findAll();
        return $this->helper->success($users, 200);
    }

    /**
     * @param ParamFetcher $paramFetcher
     *
     *
     * @REST\Post("/register/client", name="api_register_client")
     * @REST\RequestParam(name="email")
     * @REST\RequestParam(name="firstName")
     * @REST\RequestParam(name="lastName")
     * @REST\RequestParam(name="password")
     * @REST\RequestParam(name="civility")
     * @REST\RequestParam(name="phoneNumber")
     * @REST\RequestParam(name="postalCode")
     * @REST\RequestParam(name="birthDate")
     */
    public function registerClient(ParamFetcher $paramFetcher) {

        $params = $paramFetcher->all();

        $user = $this->getUserRepository()->findOneByEmail($params['email']);

        if ($user instanceof User) {
            return $this->helper->error('This email is already used');
        }

        $fosUserManager = $this->get('fos_user.user_manager');

        $user = new User();
        $form = $this->createForm(RegistrationClientType::class, $user);
        $user->setPlainPassword($params['password']);
        unset($params['password']);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }

        $user->setUsername($params['email']);
        $user->setType($user::TYPE_CLIENT);
        $user->setEnabled(1);

        $fosUserManager->updateUser($user);


        //$this->container->get('app.mail.manager')->sendConfirmationEmailMessage($user);

        return $this->json(array(
            'newUser' => array(
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'birthDate' => $user->getBirthdate(),
                'email' => $user->getEmail(),
                'type' => $user->getType(),
            )
        ));

    }


    /**
     * @return View
     *
     * @REST\Get("/clients", name="api_list_clients")
     *
     */
    public function getClients()
    {
        $clients = $this->getUserRepository()->findBy(array("type" => 1));
        return $this->helper->success($clients, 200);
    }

    /**
     * @param ParamFetcher $paramFetcher
     *
     *
     * @REST\Post("/register/restorer", name="api_register_restorer")
     * @REST\RequestParam(name="email")
     * @REST\RequestParam(name="firstName")
     * @REST\RequestParam(name="lastName")
     * @REST\RequestParam(name="password")
     * @REST\RequestParam(name="civility")
     * @REST\RequestParam(name="phoneNumber")
     * @REST\RequestParam(name="postalCode")
     * @REST\RequestParam(name="city")
     * @REST\RequestParam(name="address")
     * @REST\RequestParam(name="birthDate")
     */
    public function registerRestorer(ParamFetcher $paramFetcher) {

        $params = $paramFetcher->all();

        $user = $this->getUserRepository()->findOneByEmail($params['email']);

        if ($user instanceof User) {
            return $this->helper->error('This email is already used');
        }

        $fosUserManager = $this->get('fos_user.user_manager');

        $user = new User();
        $form = $this->createForm(RegistrationRestorerType::class, $user);
        $user->setPlainPassword($params['password']);
        unset($params['password']);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }

        $user->setUsername($params['email']);
        $user->setType($user::TYPE_RESTORER);
        $user->addRole('ROLE_ADMIN');
        $user->setEnabled(1);
        $fosUserManager->updateUser($user);

        //$this->container->get('app.mail.manager')->sendConfirmationEmailMessage($user);

        return $this->json(array(
            'newUser' => array(
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'birthDate' => $user->getBirthdate(),
                'email' => $user->getEmail(),
                'type' => $user->getType(),
                'phoneNumber' => $user->getPhoneNumber(),
                'postalCode' => $user->getPostalCode(),
                'address' => $user->getAddress(),
                'addressComplement' => $user->getAddressComplement(),
            )
        ));

    }

    /**
     * @return View
     *
     * @REST\Get("/restorers", name="api_list_restorers")
     *
     */
    public function getRestorers()
    {
        $restorers = $this->getUserRepository()->findBy(array('type' => 2));
        return $this->helper->success($restorers, 200);
    }



}
