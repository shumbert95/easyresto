<?php

namespace AppBundle\Model;

use Symfony\Component\HttpFoundation\Request;

class RestaurantSearch
{
    protected $name;

    protected $longitude;

    protected $latitude;

    protected $exact;

    public function __construct(){}

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setExact($exact)
    {
        $this->exact = $exact;
        return $this;
    }

    public function isExact()
    {
        return $this->exact;
    }
}