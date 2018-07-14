<?php

namespace AppBundle\SearchRepository;

use AppBundle\Entity\Content;
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

    public function findByNameAndRestaurant($name,Restaurant $restaurant) {
        $boolQuery = new BoolQuery();
        $nestedQuery = new Nested();
        $fieldQueryStatus = new Match();

        $fieldQueryStatus->setFieldQuery('name', $name);
        $fieldQueryStatus->setFieldMinimumShouldMatch('name','100%');
        $boolQuery->addMust($fieldQueryStatus);



        $nestedQuery->setPath('restaurant')
            ->setQuery(new Match('restaurant.id', $restaurant->getId()));
        $boolQuery->addMust($nestedQuery);

        $ingredients = $this->find($boolQuery);

        return $ingredients ? $ingredients[0] : $ingredients;

    }

    public function findByContent(Content $content) {
        $boolQuery = new BoolQuery();
        $nestedQuery = new Nested();
        $fieldQueryStatus = new Match();

        $fieldQueryStatus->setFieldQuery('status', Ingredient::STATUS_ONLINE);
        $boolQuery->addMust($fieldQueryStatus);

        $nestedQuery->setPath('contents')
            ->setQuery(new Match('contents.id', $content->getId()));
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

    public function findByNameAndRestaurantBest(Restaurant $restaurant, $name)
    {
        $boolQuery = new BoolQuery();
        $nestedQuery = new Nested();

        $nestedQuery->setPath('restaurant')
            ->setQuery(new Match('restaurant.id', $restaurant->getId()));
        $boolQuery->addMust($nestedQuery);

        if($name != ""){
            $queryString = new Query\QueryString();
            $queryString->setQuery("*".$name."*");
            $queryString->setDefaultField('name');
            $boolQuery->addMust($queryString);
        }

        return $this->find($boolQuery,10000);
    }
}
