<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer")
     */
    protected $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="civility", type="integer", length=1)
     */
    protected $civility;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    protected $address;

    /**
     * @var string
     *
     * @ORM\Column(name="address_complement", type="string", length=255, nullable=true)
     */
    protected $addressComplement;

    /**
     * @var Date
     *
     * @ORM\Column(name="birthDate", type="date", length=255, nullable=true)
     */
    protected $birthDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="phoneNumber", type="integer", length=10)
     */
    protected $phoneNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="postalCode", type="integer", length=5, nullable=true)
     */
    protected $postalCode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    protected $city;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Restaurant")
     * @ORM\JoinTable(name="favorite_restaurants",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="restaurant_id", referencedColumnName="id")}
     *      )
     */
    protected $favorites;


    const CIVILITY_MALE = 1;
    const CIVILITY_FEMALE = 2;

    public static $civilities = array(
        self::CIVILITY_MALE => 'Homme',
        self::CIVILITY_FEMALE => 'Femme',
    );

    const TYPE_CLIENT = 1;
    const TYPE_RESTORER = 2;
    const TYPE_EMPLOYEE = 3;


    public static $types = array(
        self::TYPE_CLIENT => 'Client',
        self::TYPE_RESTORER => 'Restaurateur',
        self::TYPE_EMPLOYEE => 'Employé'
    );

    public function __construct()
    {
        parent::__construct();
        $this->favorites = new ArrayCollection();
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    public function getFirstName() {
        return $this->firstName;
    }

    public function setFirstName($firstName) {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;

        return $this;
    }

    public function __toString()
    {
        return ''.$this->firstName . ' ' . $this->lastName;
    }

    /**
     * @return int
     */
    public function getCivility()
    {
        return $this->civility;
    }

    /**
     * @param int $civility
     */
    public function setCivility($civility)
    {
        $this->civility = $civility;
    }

    /**
     * @return Date
     */
    public function getBirthdate()
    {
        return $this->birthDate;
    }

    /**
     * @param Date $birthDate
     */
    public function setBirthdate($birthDate)
    {
        $this->birthDate = $birthDate;
    }

    /**
     * @return int
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param int $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
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
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
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
     * @return string
     */
    public function getAddressComplement()
    {
        return $this->addressComplement;
    }

    /**
     * @param string $addressComplement
     */
    public function setAddressComplement($addressComplement)
    {
        $this->addressComplement = $addressComplement;
    }

    /**
     * @return mixed
     */
    public function getFavorites()
    {
        return $this->favorites;
    }

    /**
     * @param mixed $favorites
     */
    public function setFavorites($favorites)
    {
        $this->favorites = $favorites;
    }

    public function addFavorite($favorite)
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites->add($favorite);
        }

        return $this;
    }

    public function removeFavorite($favorite)
    {
        if ($this->favorites->contains($favorite)) {
            $this->favorites->removeElement($favorite);
        }

        return $this;
    }









}