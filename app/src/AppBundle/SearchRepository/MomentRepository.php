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

class MomentRepository extends Repository
{

    public function findByName($name) {
        $boolQuery = new BoolQuery();
        $fieldQuery = new Match();

        $fieldQuery->setFieldQuery('name', $name);
        $boolQuery->addMust($fieldQuery);

        $moments = $this->find($boolQuery,10000);

        return $moments ? $moments[0] : $moments;
    }

    public function findById($id) {
        $boolQuery = new BoolQuery();
        $fieldQuery = new Match();

        $fieldQuery->setFieldQuery('id', $id);
        $boolQuery->addMust($fieldQuery);

        $moments = $this->find($boolQuery,10000);

        return $moments ? $moments[0] : $moments;
    }

    public function findByIds($ids) {
        $boolQuery = new BoolQuery();
        $idsQuery = new Query\Ids();

        $idsQuery->setIds($ids);

        $boolQuery->addMust($idsQuery);

        $moments = $this->find($boolQuery,10000);
        return $moments;
    }

    public function findAll() {
        $boolQuery = new BoolQuery();
        $fieldQueryStatus = new Match();

        $query = new Query($boolQuery);
        $query->addSort(array('name' => 'desc'));

        return $this->find($boolQuery,10000);
    }
}
