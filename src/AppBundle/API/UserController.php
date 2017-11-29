<?php

namespace AppBundle\API;

use AppBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;

class UserController extends FOSRestController
{
    public function createUser() {
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->createUser();

        $user->setPlainPassword(pass);
        $userManager->updateUser($user);
    }
}
