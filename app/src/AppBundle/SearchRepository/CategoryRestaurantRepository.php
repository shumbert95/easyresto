<?php

namespace AppBundle\SearchRepository;

use AppBundle\Entity\CategoryRestaurant;
use AppBundle\Entity\Restaurant;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\Nested;
use FOS\ElasticaBundle\Repository;

class CategoryRestaurantRepository extends Repository
{
    public function findByName($name) {
        $boolQuery = new BoolQuery();
        $fieldQuery = new Match();

        $fieldQuery->setFieldQuery('name', $name);
        $boolQuery->addMust($fieldQuery);

        $categories = $this->find($boolQuery,10000);
      
        return $categories ? $categories[0] : $categories;
    }

    public function findById($id) {
        $boolQuery = new BoolQuery();
        $fieldQuery = new Match();

        $fieldQuery->setFieldQuery('id', $id);
        $boolQuery->addMust($fieldQuery);

        $categories = $this->find($boolQuery,10000);

        return $categories ? $categories[0] : $categories;
    }

    public function findAll() {
        $boolQuery = new BoolQuery();
        $fieldQueryStatus = new Match();

        $query = new Query($boolQuery);
        $query->addSort(array('name' => 'desc'));

        return $this->find($boolQuery,10000);
    }
}
