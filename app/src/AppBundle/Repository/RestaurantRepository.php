<?php

namespace AppBundle\Repository;

class RestaurantRepository extends \Doctrine\ORM\EntityRepository
{

    function findByUser($user){
        $query = $this->createQueryBuilder('r')
            ->select('r')
            ->leftJoin('r.users', 'u')
            ->addSelect('u');

        return $query->add('where', $query->expr()->in('u', ':u'))
            ->setParameter('u', $user)
            ->getQuery()
            ->getResult();
    }
}