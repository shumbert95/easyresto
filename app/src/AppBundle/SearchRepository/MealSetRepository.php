<?php

namespace AppBundle\SearchRepository;

use AppBundle\Entity\Content;
use AppBundle\Entity\Restaurant;
use AppBundle\Entity\TabMeal;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\Nested;
use Elastica\Query\Term;
use FOS\ElasticaBundle\Repository;

class MealSetRepository extends Repository
{
    public function findById($id) {
        $boolQuery = new BoolQuery();
        $fieldQuery = new Match();

        $fieldQuery->setFieldQuery('id', $id);
        $boolQuery->addMust($fieldQuery);

        $mealSets = $this->find($boolQuery,10000);

        return $mealSets ? $mealSets[0] : $mealSets;
    }

    public function findByRestaurant(Restaurant $restaurant) {
        $boolQuery = new BoolQuery();
        $nestedQuery = new Nested();

        $nestedQuery->setPath('restaurant')->setQuery(new Match('restaurant.id',$restaurant->getId()));
        $boolQuery->addMust($nestedQuery);

        $mealSets = $this->find($boolQuery,10000);

        return $mealSets;
    }

    public function findIfExists(Content $mealSet, Content $meal) {
        $boolQuery = new BoolQuery();
        $nestedQuery = new Nested();
        $fieldQuery = new Match();

        $fieldQuery->setFieldQuery('id', $mealSet->getId());
        $boolQuery->addMust($fieldQuery);

        $nestedQuery->setPath('mealSetElements.content')->setQuery(new Match('mealSetElements.content.id',$meal->getId()));
        $boolQuery->addMust($nestedQuery);

        $contents = $this->find($boolQuery,10000);

        return isset($contents[0]) ? true : false;
    }
}
