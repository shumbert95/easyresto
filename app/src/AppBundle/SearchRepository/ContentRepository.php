<?php

namespace AppBundle\SearchRepository;

use AppBundle\Entity\TabMeal;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\Nested;
use FOS\ElasticaBundle\Repository;

class ContentRepository extends Repository
{
    public function findByTab(TabMeal $tab) {
        $boolQuery = new BoolQuery();
        $nestedQuery = new Nested();

        $nestedQuery->setPath('tab')
            ->setQuery(new Match('tab.id', $tab->getId()));
        $boolQuery->addMust($nestedQuery);

        $query = new Query($boolQuery);
        $query->addSort(array('position' => 'asc'));


        return $this->find($query);
    }
}
