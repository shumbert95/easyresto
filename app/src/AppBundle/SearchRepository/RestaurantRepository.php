<?php

namespace AppBundle\SearchRepository;

use AppBundle\Entity\Restaurant;
use AppBundle\Model\RestaurantSearch;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use FOS\ElasticaBundle\Repository;

class RestaurantRepository extends Repository
{

    public function findById($id) {
        $boolQuery = new BoolQuery();
        $fieldQuery = new Match();

        $fieldQuery->setFieldQuery('id', $id);
        $boolQuery->addMust($fieldQuery);

        return $this->find($boolQuery);
    }

    public function search(RestaurantSearch $restaurantSearch)
    {
        $boolQuery = new BoolQuery();
        $fieldQuery = new Match();

        $fieldQuery->setFieldQuery('status', Restaurant::STATUS_ONLINE);
        $boolQuery->addMust($fieldQuery);

        $filter = new Query\GeoDistance('position', array('lat' => $restaurantSearch->getLatitude(),
                                                                'lon' => $restaurantSearch->getLongitude()),
                                                            !$restaurantSearch->isExact() ? '10km' : '1m');

        $boolQuery->addFilter($filter);

        return $this->find($boolQuery);
    }

}
