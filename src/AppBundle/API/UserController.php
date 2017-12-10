<?php

namespace AppBundle\API;

use AppBundle\API\ApiBaseController;
use AppBundle\Entity\Restaurant;
use AppBundle\Entity\User;
use AppBundle\Form\RegistrationType;
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
     * @return View
     *
     * @REST\Post("/users/create", name="api_create_user")
     * @REST\RequestParam(name="email")
     * @REST\RequestParam(name="firstName")
     * @REST\RequestParam(name="lastName")
     * @REST\RequestParam(name="password")
     * @REST\RequestParam(name="type")
     */
    public function createUser(ParamFetcher $paramFetcher) {


        $fosUserManager = $this->get('fos_user.user_manager');

        $user = new User();

        $params = $paramFetcher->all();
        $form = $this->createForm(RegistrationType::class, $user);
        $user->setPlainPassword($params['password']);
        unset($params['password']);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->helper->error($form->getErrors());
        }

        $user->setUsername($params['email']);
        $fosUserManager->updateUser($user);

        $this->container->get('app.mail.manager')->sendConfirmationEmailMessage($user);

        return $this->helper->success($user, 200);

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

}
