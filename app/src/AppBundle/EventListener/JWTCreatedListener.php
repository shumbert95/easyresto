<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;


class JWTCreatedListener {
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack, ContainerInterface $container)
    {
        $this->requestStack = $requestStack;
        $this->container = $container;
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $doctrine = $this->container->get('doctrine');
        $payload       = $event->getData();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $type = $user->getType();
        $payload['type'] = $type;

        if ($type == User::TYPE_CLIENT) {
            $client = $doctrine->getRepository('AppBundle:Client')->findByUser($user);
            $payload['client'] = $client;
        } else {
            $restaurant = $doctrine->getRepository('AppBundle:Restaurant')->findByUser($user);
            $payload['restaurant'] = $restaurant;
        }

        $event->setData($payload);
    }
}
