<?php

namespace AppBundle\SearchRepository;

use AppBundle\Entity\CategoryRestaurant;
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

        $categories = $this->find($boolQuery);

        return $categories[0];
    }

    public function findById($id) {
        $boolQuery = new BoolQuery();
        $fieldQuery = new Match();

        $fieldQuery->setFieldQuery('id', $id);
        $boolQuery->addMust($fieldQuery);

        $categories = $this->find($boolQuery);

        return $categories[0];
    }

    public function findAll() {
        $boolQuery = new BoolQuery();
        $fieldQueryStatus = new Match();

        $fieldQueryStatus->setFieldQuery('status', CategoryRestaurant::STATUS_ONLINE);
        $boolQuery->addMust($fieldQueryStatus);

        $query = new Query($boolQuery);
        $query->addSort(array('name' => 'desc'));

        return $this->find($boolQuery);
    }
}
