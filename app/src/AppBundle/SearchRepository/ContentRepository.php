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

class ContentRepository extends Repository
{
    public function findByTab(TabMeal $tab, $type = null) {
        $boolQuery = new BoolQuery();
        $nestedQuery = new Nested();
        $fieldQuery = new Match();
        $fieldQueryStatus = new Match();

        $fieldQueryStatus->setFieldQuery('status', Content::STATUS_ONLINE);
        $boolQuery->addMust($fieldQueryStatus);

        if ($type) {
            $fieldQuery->setFieldQuery('type', $type);
            $boolQuery->addMust($fieldQuery);
        }

        $nestedQuery->setPath('tab')
            ->setQuery(new Match('tab.id', $tab->getId()));
        $boolQuery->addMust($nestedQuery);

        $query = new Query($boolQuery);
        $query->addSort(array('position' => 'asc'));

        return $this->find($query);
    }

    public function findByRestaurant(Restaurant $restaurant, $type = Content::TYPE_MEAL) {
        $boolQuery = new BoolQuery();
        $nestedQuery = new Nested();
        $fieldQuery = new Match();
        $fieldQueryStatus = new Match();

        $fieldQueryStatus->setFieldQuery('status', Content::STATUS_ONLINE);
        $boolQuery->addMust($fieldQueryStatus);

        $fieldQuery->setFieldQuery('type', $type);
        $boolQuery->addMust($fieldQuery);

        $nestedQuery->setPath('restaurant')
            ->setQuery(new Match('restaurant.id', $restaurant->getId()));
        $boolQuery->addMust($nestedQuery);

        $query = new Query($boolQuery);
        $query->addSort(array('id' => 'asc'));

        return $this->find($query);
    }

    public function findById($id) {
        $boolQuery = new BoolQuery();
        $fieldQuery = new Match();

        $fieldQuery->setFieldQuery('id', $id);
        $boolQuery->addMust($fieldQuery);

        $contents = $this->find($boolQuery);

        return $contents ? $contents[0] : $contents;
    }

    public function findByIds($ids,Restaurant $restaurant, $reservation = false) {
        $boolQuery = new BoolQuery();
        $idsQuery = new Query\Ids();
        $nestedQuery = new Nested();
        $fieldQuery = new Match();

        if($reservation){
            $fieldQuery->setFieldQuery('type', Content::TYPE_MEAL);
            $boolQuery->addMust($fieldQuery);
        }

        $nestedQuery->setPath('restaurant')->setQuery(new Match('restaurant.id',$restaurant->getId()));
        $boolQuery->addMust($nestedQuery);

        $idsQuery->setIds($ids);
        $boolQuery->addMust($idsQuery);

        $contents = $this->find($boolQuery,10000);
        return $contents;
    }

    public function findByType($type) {
        $boolQuery = new BoolQuery();
        $fieldQuery = new Match();

        $fieldQuery->setFieldQuery('status',Restaurant::STATUS_ONLINE);
        $boolQuery->addMust($fieldQuery);

        $fieldQuery->setFieldQuery('type', $type);
        $boolQuery->addMust($fieldQuery);

        $contents = $this->find($boolQuery, 10000);
        return $contents;
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

    public function findAll() {
        $boolQuery = new BoolQuery();
        $fieldQueryStatus = new Match();

        $query = new Query($boolQuery);
        $query->addSort(array('name' => 'desc'));

        return $this->find($boolQuery,10000);
    }
}
