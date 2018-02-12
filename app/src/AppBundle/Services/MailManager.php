<?php

namespace AppBundle\Services;

use AppBundle\Entity\Mail;
use AppBundle\Entity\User;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use AppBundle\Services\BaseManager;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;


class MailManager extends BaseManager
{

    /**
     * @var TwigEngine $template
     */
    private $template;
    /**
     * @var \Swift_Mailer $mailer
     */
    private $mailer;
    /**
     * @var Router
     */
    private $router;

    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;

    private $fromEmail;

    private $fromName;

    private $baseUrl;

    /**
     * @param EntityManager $em
     * @param TwigEngine $template
     * @param \Swift_Mailer $mailer
     * @param Router $router
     * @param TokenGeneratorInterface $tokenGenerator
     */
    public function __construct(Container $container, TwigEngine $template, \Swift_Mailer $mailer, Router $router, TokenGeneratorInterface $tokenGenerator, $fromEmail, $fromName, $baseUrl)
    {
        parent::__construct($container);
        $this->template = $template;
        $this->router = $router;
        $this->mailer  = $mailer;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $this->baseUrl = $baseUrl;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function sendConfirmationEmailMessage(User $user)
    {
        $user->setConfirmationToken($this->tokenGenerator->generateToken());
        $this->sendMail('mail.confirmation', 'Valider votre compte', $user, [
            'link' => $this->baseUrl . $this->router->generate('fos_user_registration_check_email', array('token' => $user->getConfirmationToken()), true)
        ]);
    }

    /**
     * @param String $code
     * @param $recipients
     * @param array $parameters
     * @throws \Exception
     * @throws \Twig\Error\Error
     */
    public function sendMail($template, $subject, $recipients, array $parameters = [])
    {
        $container = $this->getContainer();
        $mailer = $this->mailer;

        if (!is_array($recipients) || 1 === count($recipients)) {
            $recipients = array($recipients);
        }

        $message = \Swift_Message::newInstance();
        foreach ($recipients as $recipient) {
            $mailer->send(
                $message->setSubject($subject)
                    ->setTo($recipient->getEmail())
                    ->setFrom([$this->fromEmail => $this->fromName])
                    ->setBody(
                        $this->template->render('mails/'.$template.'.html.twig', $parameters),
                        'text/html',
                        'utf-8'
                    )
            );
        }
    }
}