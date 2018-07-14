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

class TagRepository extends Repository
{

    public function findByName($name) {
        $boolQuery = new BoolQuery();
        $fieldQuery = new Match();

        $fieldQuery->setFieldQuery('name', $name);
        $boolQuery->addMust($fieldQuery);

        $tags = $this->find($boolQuery,10000);

        return $tags ? $tags[0] : $tags;
    }

    public function findById($id) {
        $boolQuery = new BoolQuery();
        $fieldQuery = new Match();

        $fieldQuery->setFieldQuery('id', $id);
        $boolQuery->addMust($fieldQuery);

        $tags = $this->find($boolQuery,10000);

        return $tags ? $tags[0] : $tags;
    }

    public function findByIds($ids) {
        $boolQuery = new BoolQuery();
        $idsQuery = new Query\Ids();

        $idsQuery->setIds($ids);

        $boolQuery->addMust($idsQuery);

        $tags = $this->find($boolQuery,10000);
        return $tags;
    }

    public function findAll($restaurant) {
        $boolQuery = new BoolQuery();
        $fieldQueryStatus = new Match();

        $query = new Query($boolQuery);
        $query->addSort(array('name' => 'asc'));

        return $this->find($boolQuery,10000);
    }
}
