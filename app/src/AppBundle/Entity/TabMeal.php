<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tab_meal")
 */
class TabMeal
{
    const STATUS_OFFLINE = false;
    const STATUS_ONLINE = true;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
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
     * @var int
     *
     * @ORM\Column(name="position", type="integer", length=10)
     */
    protected $position;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    protected $status;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Restaurant")
     * @ORM\JoinColumn(nullable=false)
     */
    private $restaurant;

    /**
     * @var array
     * @ORM\Column(name="meals_ids", type="text", nullable=true)
     */
    protected $mealsIds;

    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param String $name
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * @return Boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param Boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
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
     * @return array
     */
    public function getMealsIds()
    {
        return $this->mealsIds;
    }

    /**
     * @param array $mealsIds
     */
    public function setMealsIds($mealsIds)
    {
        $this->mealsIds = $mealsIds;
    }




    public function __toString()
    {
        return ''.$this->name;
    }

}
