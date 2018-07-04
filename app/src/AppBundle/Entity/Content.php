<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 * @ORM\Entity
 * @ORM\Table(name="content")
 */
class Content
{

    /**
     * @ORM\ManyToOne(targetEntity="Restaurant")
     * @ORM\JoinColumn(name="restaurant_id")
     */
    protected $restaurant;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TabMeal")
     * @ORM\JoinColumn(name="tab_id")
     */
    protected $tab;


    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer")
     */
    protected $type;

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
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", length=10, nullable=true)
     */
    protected $price;

    /**
     * @var bool
     *
     * @ORM\Column(name="availability", type="boolean", nullable=true)
     */
    protected $availability;

    /**
     * @var int
     *
     * @ORM\Column(name="initial_stock", type="integer", length=10, nullable=true)
     */
    protected $initialStock;

    /**
     * @var int
     *
     * @ORM\Column(name="current_stock", type="integer", length=10, nullable=true)
     */
    protected $currentStock;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer", length=10, nullable=true)
     */
    protected $position;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    protected $status;

    const TYPE_MEAL = 1;
    const TYPE_CATEGORY = 2;


    public static $types = array(
        self::TYPE_MEAL => 'meal',
        self::TYPE_CATEGORY => 'category'
    );

    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * @param mixed $restaurant
     */
    public function setRestaurant($restaurant)
    {
        $this->restaurant = $restaurant;
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
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getTab()
    {
        return $this->tab;
    }

    /**
     * @param mixed $tab
     */
    public function setTab($tab)
    {
        $this->tab = $tab;
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

    public function __toString()
    {
        return ''.$this->name;
    }

}