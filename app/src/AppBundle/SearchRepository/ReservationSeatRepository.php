<?php

namespace AppBundle\SearchRepository;

use AppBundle\Entity\Content;
use AppBundle\Entity\Reservation;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\Nested;
use FOS\ElasticaBundle\Repository;

class ReservationSeatRepository extends Repository
{
    public function findById($id) {
        $boolQuery = new BoolQuery();
        $fieldQuery = new Match();

        $fieldQuery->setFieldQuery('id', $id);
        $boolQuery->addMust($fieldQuery);

        $reservationContents = $this->find($boolQuery,10000);

        return $reservationContents ? $reservationContents[0] : $reservationContents;
    }

    public function findByContentAndReservation($content, $reservation) {
        $boolQuery = new BoolQuery();
        $nestedQuery = new Nested();
        $secondNestedQuery = new Nested();

        $nestedQuery->setPath('reservation')->setQuery(new Match('reservation.id',$reservation));
        $boolQuery->addMust($nestedQuery);

        $secondNestedQuery->setPath('content')->setQuery(new Match('content.id',57));
        $boolQuery->addMust($secondNestedQuery);

        $reservationContents = $this->find($boolQuery,1);

        return $reservationContents ? $reservationContents[0] : null;
    }
}
