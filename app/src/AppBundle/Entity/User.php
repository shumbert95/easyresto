<?php
namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

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
}