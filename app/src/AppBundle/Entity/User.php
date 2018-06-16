<?php
namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;

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
     * @var Date
     *
     * @ORM\Column(name="birthDate", type="date", length=255)
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
     * @ORM\Column(name="postalCode", type="integer", length=5)
     */
    protected $postalCode;

    const CIVILITY_MALE = 1;
    const CIVILITY_FEMALE = 2;

    public static $civilities = array(
        self::CIVILITY_MALE => 'Homme',
        self::CIVILITY_FEMALE => 'Femme',
    );

    const TYPE_CLIENT = 1;
    const TYPE_EMPLOYEE = 2;
    const TYPE_RESTORER = 3;

    public static $types = array(
        self::TYPE_CLIENT => 'Client',
        self::TYPE_EMPLOYEE => 'Employé',
        self::TYPE_RESTORER => 'Restaurateur'
    );

    public function __construct()
    {
        parent::__construct();
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


}