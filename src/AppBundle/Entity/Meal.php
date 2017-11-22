<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="meal")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MealRepository")
 */
class Meal
{

    /**
     * @ORM\ManyToOne(targetEntity="Restaurant)
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id")
     */
    protected $restaurant;


    /**
     * @ORM\OneToMany(targetEntity="MealCategory", mappedBy="meal")
     */
    protected $mealCategories;

    public function __construct()
    {
        $this->mealCategories = new ArrayCollection();
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    protected $description;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", length=10)
     */
    protected $price;

    /**
     * @var bool
     *
     * @ORM\Column(name="availability", type="boolean")
     */
    protected $availability;

    /**
     * @var int
     *
     * @ORM\Column(name="initial_stock", type="integer", length=10)
     */
    protected $initialStock;

    /**
     * @var int
     *
     * @ORM\Column(name="current_stock", type="integer", length=10)
     */
    protected $currentStock;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    protected $status;

    /**
     * @return \AppBundle\Entity\Restaurant
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * @param \AppBundle\Entity\Restaurant $restaurant
     */
    public function setRestaurant($restaurant)
    {
        $this->restaurant = $restaurant;
    }

    /**
     * @return \AppBundle\Entity\MealCategory
     */
    public function getMealCategories()
    {
        return $this->mealCategories;
    }

    /**
     * @param \AppBundle\Entity\MealCategory $mealCategories
     */
    public function setMealCategories($mealCategories)
    {
        $this->mealCategories = $mealCategories;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return bool
     */
    public function isAvailability()
    {
        return $this->availability;
    }

    /**
     * @param bool $availability
     */
    public function setAvailability($availability)
    {
        $this->availability = $availability;
    }

    /**
     * @return int
     */
    public function getInitialStock()
    {
        return $this->initialStock;
    }

    /**
     * @param int $initialStock
     */
    public function setInitialStock($initialStock)
    {
        $this->initialStock = $initialStock;
    }

    /**
     * @return int
     */
    public function getCurrentStock()
    {
        return $this->currentStock;
    }

    /**
     * @param int $currentStock
     */
    public function setCurrentStock($currentStock)
    {
        $this->currentStock = $currentStock;
    }

    /**
     * @return bool
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }




}