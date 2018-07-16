<?php

namespace AppBundle\Model;

use Symfony\Component\HttpFoundation\Request;

class RestaurantSearch
{
    protected $name;

    protected $longitude = 0;

    protected $latitude = 0;

    protected $exact;

    protected $category = 0;

    protected $moment = 0;

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
    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    public function getMoment()
    {
        return $this->moment;
    }

    public function setMoment($moment)
    {
        $this->moment = $moment;
    }




}