<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="invoice")
 */
class Invoice
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Restaurant")
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id")
     */
    protected $restaurant;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $price;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Content")
     * @ORM\JoinTable(name="invoice_meal",
     *      joinColumns={@ORM\JoinColumn(name="menu_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="meal_id", referencedColumnName="id")}
     *      )
     */
    protected $meals;

    /**
     * @ORM\Column(type="datetime", length=255, nullable=true)
     */
    protected $expectedTime;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    protected $status;


    public function __construct(){
        $this->meals = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;

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

        return $this;
    }

    public function getMeals()
    {
        return $this->meals;
    }

    public function setMeals($meals)
    {
        $this->meals = $meals;
    }

    public function addMeal($meal)
    {
        if (!$this->meals->contains($meal)) {
            $this->meals->add($meal);
        }

        return $this;
    }

    public function removeMeal($meal)
    {
        if ($this->meals->contains($meal)) {
            $this->meals->remove($meal);
        }

        return $this;
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

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }



}


