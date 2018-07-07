<?php
namespace AppBundle\Repository;
use AppBundle\Entity\User;
use AppBundle\Entity\Restaurant;

class RestaurantRepository extends \Doctrine\ORM\EntityRepository
{
    public function getRestaurantByOwner(User $user){
        $qb = $this->createQueryBuilder('r');

        $qb->join('r.users', 'u','WHERE', 'u=:user')
            ->setParameter('user',$user);

        return $qb->getQuery()->getResult();
    }

}