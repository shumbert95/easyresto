<?php

namespace AppBundle\SearchRepository;

use AppBundle\Entity\Content;
use AppBundle\Entity\MealSet;
use AppBundle\Entity\Restaurant;
use AppBundle\Entity\TabMeal;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\Nested;
use Elastica\Query\Term;
use FOS\ElasticaBundle\Repository;

class MealSetElementRepository extends Repository
{
    public function findById($id) {
        $boolQuery = new BoolQuery();
        $fieldQuery = new Match();

        $fieldQuery->setFieldQuery('id', $id);
        $boolQuery->addMust($fieldQuery);

        $mealSetElements = $this->find($boolQuery,10000);

        return $mealSetElements ? $mealSetElements[0] : $mealSetElements;
    }

    public function findBySet(MealSet $mealSet) {
        $boolQuery = new BoolQuery();
        $nestedQuery = new Nested();

        $nestedQuery->setPath('mealSet')->setQuery(new Match('mealSet.id',$mealSet->getId()));
        $boolQuery->addMust($nestedQuery);


        $mealSetElements = $this->find($boolQuery,10000);


        return $mealSetElements;
    }


    public function findBySetAndContent(MealSet $mealSet, Content $meal) {
        $boolQuery = new BoolQuery();
        $nestedQuery = new Nested();
        $secondNestedQuery = new Nested();


        $nestedQuery->setPath('content')->setQuery(new Match('content.id',$meal->getId()));
        $boolQuery->addMust($nestedQuery);

        $secondNestedQuery->setPath('mealSet')->setQuery(new Match('mealSet.id',$mealSet->getId()));
        $boolQuery->addMust($secondNestedQuery);

        $mealSetElements = $this->find($boolQuery,10000);

        return $mealSetElements ? $mealSetElements[0] : $mealSetElements;
    }
}
