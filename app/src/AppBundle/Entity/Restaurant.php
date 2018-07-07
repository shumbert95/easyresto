<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * @ORM\Entity
 * @ORM\Table(name="restaurant")
 */
class Restaurant
{
    const STATUS_OFFLINE = false;
    const STATUS_ONLINE = true;
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
     *      joinColumns={@ORM\JoinColumn(name="restaurant_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
     */
    protected $users;


    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\CategoryRestaurant", cascade={"persist"})
     * @ORM\JoinTable(name="restaurant_categories",
     *      joinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="restaurant_id", referencedColumnName="id")}
     *      )
     */
    protected $categories;

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
     * @var float
     *
     * @ORM\Column(name="latitude", type="float", length=255,nullable=true)
     */
    protected $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float", length=255,nullable=true)
     */
    protected $longitude;


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
     * @var string
     * @ORM\Column(name="picture", type="text", nullable=true)
     */
    protected $picture;


    /**
     * @var array
     * @ORM\Column(name="schedule", type="text", nullable=true)
     */
    protected $schedule;

    /**
     * @var bool
     *
     * @ORM\Column(name="open", type="boolean")
     */
    protected $open;

    /**
     * @var int
     *
     * @ORM\Column(name="seats", type="integer", length=10, nullable=true)
     */
    protected $seats;

    /**
     * @var int
     *
     * @ORM\Column(name="current_seats", type="integer", length=10, nullable=true)
     */
    protected $currentSeats;

    /**
     * @var float
     *
     * @ORM\Column(name="average_note",type="float")
     */
    protected $averageNote = 0;


    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    protected $status;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $user
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    public function addUser($user)
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser($user)
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param mixed $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    public function addCategory($category)
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory($category)
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
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

    /**
     * @return int
     */
    public function getSeats()
    {
        return $this->seats;
    }

    /**
     * @param int $seats
     */
    public function setSeats($seats)
    {
        $this->seats = $seats;
    }

    /**
     * @return int
     */
    public function getCurrentSeats()
    {
        return $this->currentSeats;
    }

    /**
     * @param int $currentSeats
     */
    public function setCurrentSeats($currentSeats)
    {
        $this->currentSeats = $currentSeats;
    }

    /**
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param string $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    /**
     * @return float
     */
    public function getAverageNote()
    {
        return $this->averageNote;
    }

    /**
     * @param float $averageNote
     */
    public function setAverageNote($averageNote)
    {
        $this->averageNote = $averageNote;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    public function getLocation()
    {
        return $this->latitude . ',' . $this->longitude;
    }

    public function __toString()
    {
        return ''.$this->name;
    }
}