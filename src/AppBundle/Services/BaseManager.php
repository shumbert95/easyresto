<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;

abstract class BaseManager
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getEntityManager();
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @param mixed $object
     *
     * @return BaseManager
     */
    public function save($object, $flush = true)
    {
        $entityManager = $this->getEntityManager();
        try {
            if (!$entityManager->isOpen()) {
                $entityManager = $this->container->get('doctrine')->resetManager();
            }
            $entityManager->persist($object);
            if ($flush) {
                $entityManager->flush();
            }
        } catch (UniqueConstraintViolationException $e) {

        }
        return $this;
    }

    /**
     * @param mixed $object
     *
     * @return BaseManager
     */
    public function delete($object, $flush = true)
    {
        $entityManager = $this->getEntityManager();

        $entityManager->remove($object);
        if ($flush) {
            $entityManager->flush();
        }

        return $this;
    }
}
