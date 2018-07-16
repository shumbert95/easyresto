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

    public function findAll() {
        $boolQuery = new BoolQuery();

        $query = new Query($boolQuery);
        $query->addSort(array('name' => 'desc'));

        return $this->find($boolQuery,10000);
    }

    public function findByNameBest($name)
    {
        $boolQuery = new BoolQuery();
        $fieldQuery = new Match();


        if($name != ""){
            $queryString = new Query\QueryString();
            $queryString->setQuery("*".$name."*");
            $queryString->setDefaultField('name');
            $boolQuery->addMust($queryString);
        }

        return $this->find($boolQuery,10000);
    }

    public function findByNameUpsert($name) {
        $boolQuery = new BoolQuery();
        $fieldQueryStatus = new Match();
        $fieldQuery = new Match();

        $fieldQueryStatus->setFieldQuery('name', $name);
        $fieldQueryStatus->setFieldMinimumShouldMatch('name','100%');
        $boolQuery->addMust($fieldQueryStatus);

        $tags = $this->find($boolQuery);

        return $tags ? $tags[0] : $tags;

    }
}
