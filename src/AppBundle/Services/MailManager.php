<?php

namespace AppBundle\Services;

use AppBundle\Entity\Mail;
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
     * @param ContainerInterface $container
     * @param TwigEngine $template
     */
    public function __construct(ContainerInterface $container, TwigEngine $template)
    {
        parent::__construct($container);
        $this->template = $template;
    }

    /**
     * @param String $code
     * @param $recipients
     * @param array $parameters
     * @throws \Exception
     * @throws \Twig\Error\Error
     */
    public function sendMail($code, $recipients, array $parameters = [])
    {
        $mail = $this->getEntityManager()->getRepository('AppBundle:Mail')->findOneBy(array('code' => $code));
        $container = $this->getContainer();
        $mailer = $container->get('mailer');

        if (!$mail instanceof Mail) {
            return;
        }

        if (!is_array($recipients) || 1 === count($recipients)) {
            $recipients = [$recipients[0]];
        }

        $message = \Swift_Message::newInstance();
        foreach ($recipients as $recipient) {
            $mailer->send(
                $message->setSubject($mail->getSubject())
                    ->setFrom([$mail->getFromEmail() => $mail->getFromName()])
                    ->setTo($recipient->getEmail())
                    ->setBody(
                        $this->template->render('mails/mail-template.html.twig', [
                            'title' => $mail->getSubject(),
                            'content' => $this->buildBody($mail->getContent(), $parameters),
                        ]),
                        'text/html',
                        'utf-8'
                    )
            );
        }
    }

    /**
     * @param $template
     * @param array $parameters
     * @return string
     */
    private function buildBody($template, array $parameters)
    {
        return nl2br(str_replace(
            array_map(function ($parameter) {return '%' . $parameter . '%';}, array_keys($parameters)),
            array_values($parameters),
            $template
        ));
    }
}