<?php

namespace AppBundle\SearchRepository;

use AppBundle\Entity\Restaurant;
use AppBundle\Model\RestaurantSearch;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\Nested;
use FOS\ElasticaBundle\Repository;

class RestaurantRepository extends Repository
{
    const MAX = 100;

    public function search(RestaurantSearch $restaurantSearch)
    {

        $boolQuery = new \Elastica\Query\BoolQuery();

        $fieldQuery = new \Elastica\Query\Match();

        $fieldQuery->setFieldQuery('status', Restaurant::STATUS_ONLINE);
        $boolQuery->addMust($fieldQuery);


        $filter = new Query\GeoDistance('position', array('lat' => $restaurantSearch->getLatitude(),
                                                                'lon' => $restaurantSearch->getLongitude()), '10km');

        $boolQuery->addFilter($filter);

        return $this->find($boolQuery);
    }

}
