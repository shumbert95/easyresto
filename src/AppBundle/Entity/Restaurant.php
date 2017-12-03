<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RestaurantRepository")
 * @ORM\Table(name="restaurant")
 */
class Restaurant
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinTable(name="restaurant_users",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="restaurant_id", referencedColumnName="id")}
     *      )
     */
    protected $users;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CategoryRestaurant", mappedBy="restaurants")
     */
    protected $mealCategories;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="city", type="string", length=255)
     */
    protected $city;

    /**
     * @var int
     * @Assert\NotBlank()
     * @ORM\Column(name="postal_code", type="integer", length=10)
     */
    protected $postalCode;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="address", type="string", length=255)
     */
    protected $address;

    /**
     * @var string
     *
     * @ORM\Column(name="address_complement", type="string", length=255, nullable=true)
     */
    protected $addressComplement;


    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="phone", type="string")
     */
    protected $phone;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="description", type="text")
     */
    protected $description;

    /**
     * @var array
     * @ORM\Column(name="schedule", type="array", nullable=true)
     */
    protected $schedule;

    /**
     * @var bool
     *
     * @ORM\Column(name="open", type="boolean")
     */
    protected $open;



    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    protected $status;

    public function __construct()
    {
        $this->mealCategories = new ArrayCollection();
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

    /**
     * @return mixed
     */
    public function getMealCategories()
    {
        return $this->mealCategories;
    }

    /**
     * @param mixed $mealCategories
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
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $town
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return int
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param int $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return String
     */
    public function getAddressComplement()
    {
        return $this->addressComplement;
    }

    /**
     * @param String $addressComplement
     */
    public function setAddressComplement($addressComplement)
    {
        $this->addressComplement = $addressComplement;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
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
     * @return string
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * @param string $schedule
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * @return bool
     */
    public function isOpen()
    {
        return $this->open;
    }

    /**
     * @param bool $open
     */
    public function setOpen($open)
    {
        $this->open = $open;
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