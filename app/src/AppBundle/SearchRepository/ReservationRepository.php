<?php

namespace AppBundle\SearchRepository;

use AppBundle\Entity\Restaurant;
use AppBundle\Entity\User;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\Range;
use Elastica\Query\Nested;
use FOS\ElasticaBundle\Repository;

class ReservationRepository extends Repository
{
    public function findByRestaurant(Restaurant $restaurant, \DateTime $dateFrom, \DateTime $dateTo) {
        $boolQuery = new BoolQuery();
        $nestedQuery = new Nested();

        $boolQuery->addMust(new Range('date', array(
            'gte' => \Elastica\Util::convertDate($dateFrom->getTimestamp()),
            'lte' => \Elastica\Util::convertDate($dateTo->getTimestamp())
        )));

        $nestedQuery->setPath('restaurant')
                    ->setQuery(new Match('restaurant.id', $restaurant->getId()));
        $boolQuery->addMust($nestedQuery);
        return $this->find($boolQuery,1000);
    }

    public function findByUser(User $user, \DateTime $dateFrom, \DateTime $dateTo) {
        $boolQuery = new BoolQuery();
        $nestedQuery = new Nested();

        $boolQuery->addMust(new Range('date', array(
            'gte' => \Elastica\Util::convertDate($dateFrom->getTimeStamp()),
            'lte' => \Elastica\Util::convertDate($dateTo->getTimeStamp())
        )));

        $nestedQuery->setPath('user')
            ->setQuery(new Match('user.id', $user->getId()));
        $boolQuery->addMust($nestedQuery);

        return $this->find($boolQuery);
    }
}