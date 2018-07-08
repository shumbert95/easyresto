<?php

namespace AppBundle\SearchRepository;

use AppBundle\Entity\Ingredient;
use AppBundle\Entity\Restaurant;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\Nested;
use FOS\ElasticaBundle\Repository;

class IngredientRepository extends Repository
{

    public function findByRestaurant(Restaurant $restaurant) {
        $boolQuery = new BoolQuery();
        $nestedQuery = new Nested();
        $fieldQueryStatus = new Match();

        $fieldQueryStatus->setFieldQuery('status', Ingredient::STATUS_ONLINE);
        $boolQuery->addMust($fieldQueryStatus);

        $nestedQuery->setPath('restaurant')
            ->setQuery(new Match('restaurant.id', $restaurant->getId()));
        $boolQuery->addMust($nestedQuery);

        $query = new Query($boolQuery);
        $query->addSort(array('position' => 'asc'));

        return $this->find($query);
    }

    public function findById($id) {
        $boolQuery = new BoolQuery();
        $fieldQuery = new Match();

        $fieldQuery->setFieldQuery('id', $id);
        $boolQuery->addMust($fieldQuery);

        $ingredients = $this->find($boolQuery,10000);

        return $ingredients ? $ingredients[0] : $ingredients;
    }

    public function findByIds($ids) {
        $boolQuery = new BoolQuery();
        $idsQuery = new Query\Ids();

        $idsQuery->setIds($ids);

        $boolQuery->addMust($idsQuery);

        $ingredients = $this->find($boolQuery,10000);
        return $ingredients;
    }
}
