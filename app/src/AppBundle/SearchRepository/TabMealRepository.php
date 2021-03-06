<?php

namespace AppBundle\SearchRepository;

use AppBundle\Entity\Restaurant;
use AppBundle\Entity\TabMeal;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\Nested;
use FOS\ElasticaBundle\Repository;

class TabMealRepository extends Repository
{
    public function findById($id) {
        $boolQuery = new BoolQuery();
        $fieldQuery = new Match();

        $fieldQuery->setFieldQuery('id', $id);
        $boolQuery->addMust($fieldQuery);

        $tabs = $this->find($boolQuery,1000);

        return $tabs ? $tabs[0] : $tabs;
    }

    public function findByRestaurant(Restaurant $restaurant) {
        $boolQuery = new BoolQuery();
        $nestedQuery = new Nested();
        $fieldQuery = new Match();

        $fieldQuery->setFieldQuery('status', TabMeal::STATUS_ONLINE);
        $boolQuery->addMust($fieldQuery);

        $nestedQuery->setPath('restaurant')
                    ->setQuery(new Match('restaurant.id', $restaurant->getId()));
        $boolQuery->addMust($nestedQuery);

        $query = new Query($boolQuery);
        $query->addSort(array('position' => 'ASC'));

        return $this->find($query,1000);
    }
}
