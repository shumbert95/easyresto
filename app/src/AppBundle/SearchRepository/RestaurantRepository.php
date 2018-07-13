<?php

namespace AppBundle\SearchRepository;

use AppBundle\Entity\Restaurant;
use AppBundle\Entity\User;
use AppBundle\Model\RestaurantSearch;
use Elastica\Aggregation\Filter;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\Nested;

use FOS\ElasticaBundle\Repository;
use http\QueryString;

class RestaurantRepository extends Repository
{

    public function findById($id) {
        $boolQuery = new BoolQuery();
        $fieldQuery = new Match();

        $fieldQuery->setFieldQuery('id', $id);
        $boolQuery->addMust($fieldQuery);

        $restaurants = $this->find($boolQuery,10000);

        return $restaurants ? $restaurants[0] : $restaurants;
    }

    public function findByOwner(User $user) {
        $boolQuery = new BoolQuery();
        $nestedQuery = new Nested();
        $fieldQuery = new Match();

        $fieldQuery->setFieldQuery('status',Restaurant::STATUS_ONLINE);
        $boolQuery->addMust($fieldQuery);

        $nestedQuery->setPath('users')->setQuery(new Match('users.id',$user->getId()));
        $boolQuery->addMust($nestedQuery);

        return $this->find($boolQuery,10000);
    }

    public function search(RestaurantSearch $restaurantSearch)
    {
        $boolQuery = new BoolQuery();
        $fieldQuery = new Match();
        $filterQuery = new BoolQuery();

        $fieldQuery->setFieldQuery('status', Restaurant::STATUS_ONLINE);
        $boolQuery->addMust($fieldQuery);

        if($restaurantSearch->getCategory() != 0){
            if(is_array($restaurantSearch->getCategory())){
                foreach($restaurantSearch->getCategory() as $category) {
                    $nestedQuery = new Nested();
                    $nestedQuery->setPath('categories')->setQuery(new Match('categories.id', $category));
                    $filterQuery->setMinimumNumberShouldMatch(1);
                    $filterQuery->addShould($nestedQuery);
                }
                $boolQuery->addMust($filterQuery);
            }
            else{
                $nestedQuery = new Nested();
                $nestedQuery->setPath('categories')->setQuery(new Match('categories.id', $restaurantSearch->getCategory()));
                $boolQuery->addMust($nestedQuery);
            }
        }


        if($restaurantSearch->getMoment() != 0){
            if(is_array($restaurantSearch->getMoment())){
                foreach($restaurantSearch->getMoment() as $moment) {
                    $nestedQuery = new Nested();
                    $nestedQuery->setPath('moments')->setQuery(new Match('moments.id', $moment));
                    $filterQuery->addShould($nestedQuery);
                }
                $boolQuery->addMust($filterQuery);
            }
            else{
                $nestedQuery = new Nested();
                $nestedQuery->setPath('moments')->setQuery(new Match('moments.id', $restaurantSearch->getMoment()));
                $boolQuery->addMust($nestedQuery);
            }
        }

        if($restaurantSearch->getName()!= ""){
            $queryString = new Query\QueryString();
            $queryString->setQuery("*".$restaurantSearch->getName()."*");
            $queryString->setDefaultField('name');
            $boolQuery->addMust($queryString);

        }
        $filter = new Query\GeoDistance('location', array('lat' => $restaurantSearch->getLatitude(),
                                                                'lon' => $restaurantSearch->getLongitude()),
                                                            !$restaurantSearch->isExact() ? '10km' : '1m');

        $boolQuery->addFilter($filter);
        return $this->find($boolQuery,10000);
    }

}
