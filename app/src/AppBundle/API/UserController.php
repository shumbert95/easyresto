<?php

namespace AppBundle\API;

use AppBundle\Entity\User;
use AppBundle\Form\RegistrationClientType;
use AppBundle\Form\RegistrationRestorerType;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserController extends ApiBaseController
{

    /**
     * @param ParamFetcher $paramFetcher
     *
     *
     * @REST\Post("/users/create", name="api_create_user")
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
        $user = $this->getUserRepository()->find($user);

        //$this->container->get('app.mail.manager')->sendConfirmationEmailMessage($user);

        return $this->json(array(
            'newUser' => array(
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'birthDate' =>  $user->getBirthDate(),
                'email' => $user->getEmail(),
                'type' => $user->getType(),
            )
        ));

    }

    /**
     *
     * @REST\Put("/profile", name="api_edit_profile")
     *
     */
    public function editProfile(Request $request)
    {
        $request_data = $request->request->all();

        $fosUserManager = $this->get('fos_user.user_manager');
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(isset($request_data['phoneNumber'])){
            $user->setPhoneNumber($request_data['phoneNumber']);
        }
        if(isset($request_data['postalCode'])){
            $user->setPostalCode($request_data['postalCode']);
        }
        if(isset($request_data['firstName'])){
            $user->setFirstName($request_data['firstName']);
        }
        if(isset($request_data['lastName'])){
            $user->setLastName($request_data['lastName']);
        }
        if(isset($request_data['email'])){
            $user->setEmail($request_data['email']);
            $user->setEmailCanonical($request_data['email']);
            $user->setUsername($request_data['email']);
            $user->setUsernameCanonical($request_data['email']);
        }
        if(isset($request_data['password'])){
            $user->setPlainPassword($request_data['password']);
        }
        if(isset($request_data['civility'])){
            $user->setCivility($request_data['civility']);
        }
        if(isset($request_data['address'])){
            $user->setAddress($request_data['address']);
        }
        if(isset($request_data['addressComplement'])){
            $user->setAddressComplement($request_data['addressComplement']);
        }
        if(isset($request_data['birthDate'])){
            $birthDate = new \DateTime($request_data['birthDate']);
            $user->setBirthDate($birthDate);
        }
        if(isset($request_data['city'])){
            $user->setCity($request_data['city']);
        }

        $fosUserManager->updateUser($user,true);
        return $this->helper->success($user, 200);
    }

    /**
     *
     * @REST\Get("/users/{id}", name="api_detail_user")
     *
     */
    public function getUserById(Request $request)
    {
        $user = $this->getUserRepository()->find($request->get('id'));
        if(!$user)
            return $this->helper->empty();
        return $this->helper->success($user, 200);
    }

    /**
     *
     * @REST\Get("/profile", name="api_detail_logged_user")
     *
     */
    public function getLoggedUser()
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(!is_object($user)){
            return $this->helper->error('Vous n\'êtes pas connecté.');
        }

        if($user->getType() == User::TYPE_RESTORER) {
            $elasticaManager = $this->container->get('fos_elastica.manager');
            $em = $this->getEntityManager();
            $restaurants = $elasticaManager->getRepository('AppBundle:Restaurant')->findByOwner($user);
            $return_data["user"] = $user;

            if(is_array($restaurants)) {
                foreach ($restaurants as $restaurant) {
                    $return_data["restaurants"][] = array("id" => $restaurant->getId());
                }
            }
            else
                $return_data["restaurant"] = array("id" => $restaurants->getId());

        }
        else
            $return_data["user"]=$user;

        return $this->helper->success($return_data, 200);
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
        if(!$users)
            return $this->helper->empty();
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


        $mailer = $this->container->get('mailer');
        $message = (new \Swift_Message('Bienvenue sur Easyresto !'))
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    '@App/mails/inscription_client.html.twig',
                    array(
                        'user' => $user,
                    )
                ),
                'text/html'
            );
        $mailer->send($message);

        return $this->json(array(
            'newUser' => array(
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'birthDate' => $user->getBirthDate(),
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
        if(!$clients)
            return $this->helper->empty();
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
        //$fosUserManager->updateUser($user);

        $mailer = $this->container->get('mailer');
        $message = (new \Swift_Message('Bienvenue sur Easyresto !'))
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    '@App/mails/inscription_restorer.html.twig',
                    array(
                        'user' => $user,
                    )
                ),
                'text/html'
            );
        $mailer->send($message);

        return $this->json(array(
            'newUser' => array(
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'birthDate' => $user->getBirthDate(),
                'email' => $user->getEmail(),
                'type' => $user->getType(),
                'phoneNumber' => $user->getPhoneNumber(),
                'postalCode' => $user->getPostalCode(),
                'address' => $user->getAddress(),
                'addressComplement' => $user->getAddressComplement(),
                'city' => $user->getCity(),
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
        if(!$restorers)
            return $this->helper->empty();
        return $this->helper->success($restorers, 200);
    }

    /**
     *
     * @REST\Post("/login/facebook", name="api_login_facebook")
     *
     */
    public function facebookLoginAction(Request $request)
    {
        $token = $request->get("access_token");

        @$tokenAppResp = file_get_contents('https://graph.facebook.com/app/?access_token=' . $token);
        if (!$tokenAppResp) {
            throw new AccessDeniedHttpException('Bad credentials Fb. 1');
        }

        @$tokenUserResp = file_get_contents('https://graph.facebook.com/me/?access_token=' . $token);
        if (!$tokenUserResp) {
            throw new AccessDeniedHttpException('Bad credentials Fb. 2');
        }

        $tokenUser = json_decode($tokenUserResp, true);
        if (!$tokenUser || !isset($tokenUser['id'])) {
            throw new AccessDeniedHttpException('Bad credentials Fb. 3');
        }

        $user = $this->getUserRepository()->findOneByEmail($tokenUser['email']);

        if ($user === null) {
            $fosUserManager = $this->get('fos_user.user_manager');
            $user = new User();
            $user->setFacebookID($tokenUser['id']);
            $user->setFacebookAccessToken($token);
            //I have set all requested data with the user's username
            //modify here with relevant data
            $user->setUsername($tokenUser['email']);
            $user->setFirstName($tokenUser['first_name']);
            $user->setLastName($tokenUser['last_name']);
            $user->setEmail($tokenUser['email']);
            $user->setCivility(1);
            $user->setType(1);
            $user->setPlainPassword(substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10 / strlen($x)))), 1, 10));
            $user->setEnabled(true);
            $fosUserManager->updateUser($user);
        }

        $jwtManager = $this->get("lexik_jwt_authentication.jwt_manager");
        $token = $jwtManager->create($user);

        return ['token' => $token];
    }





}
