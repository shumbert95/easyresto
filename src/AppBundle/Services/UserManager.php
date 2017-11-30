<?php

namespace AppBundle\Services;

use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Translation\TranslatorInterface;

class UserManager extends BaseManager
{

    /**
     * @var MailManager
     */
    private $mailManager;

    /**
     * @param ContainerInterface $container
     * @param MailManager $mailManager
     */
    public function __construct(ContainerInterface $container, MailManager $mailManager)
    {
        parent::__construct($container);
        $this->mailManager = $mailManager;
    }

    /**
     * @param User $user
     * @param string $password
     *
     * @return bool
     */
    public function isPasswordValid(User $user, $password)
    {
        $encoder = $this->getContainer()->get('security.password_encoder');
        return $encoder->isPasswordValid($user, $password);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function sendEmailResetting(User $user)
    {
        if ($user->isPasswordRequestNonExpired($this->getContainer()->getParameter('fos_user.resetting.token_ttl'))) {
            throw new HttpException(400, 'Vous ne pouvez pas regénérer votre mot de passe');
        }

        if (null === $user->getConfirmationToken()) {
            $tokenGenerator = $this->getContainer()->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
            $user->setPasswordRequestedAt(new \DateTime());
            $this->getContainer()->get('fos_user.user_manager')->updateUser($user);
        }

        $url = $this->getContainer()->getParameter('base_url') . '/reset?token=' . $user->getConfirmationToken();

        $this->mailManager->sendMail('password-reset', $user->getEmail(), array('url' => $url));
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function reset(User $user, array $params)
    {

        if ($params['token'] != $user->getConfirmationToken()) {
            throw new HttpException(400, 'Bad token');
        }

        if ($params['password'] != $params['password_confirmation']) {
            throw new HttpException(400, 'Bad password confirmation');
        }

        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]+$/', $params['password'])) {
            throw new HttpException(400, 'Bad password format');
        }

        $user->setPlainPassword($params['password']);

        $this->getContainer()->get('fos_user.user_manager')->updateUser($user);

        return $user;
    }
}
