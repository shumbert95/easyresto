<?php


namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="reservation")
 */
class Reservation{

    const STATE_CANCELED = -1;
    const STATE_PENDING = 0;
    const STATE_PAID = 1;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $state;

    /**
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    protected $total;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    protected $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_submitted", type="datetime")
     */
    protected $dateSubmitted;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_canceled", type="datetime", nullable=true)
     */
    protected $dateCanceled;

    /**
     * @var string
     *
     * @ORM\Column(name="timeStep", type="string", length=255)
     */
    protected $timeStep;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Restaurant")
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id")
     */
    protected $restaurant;

    /**
     * @ORM\Column(type="integer")
     */
    protected $nbParticipants;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_method", type="string", length=255, nullable=true)
     */
    protected $paymentMethod;

    public function __construct(User $user, Restaurant $restaurant){
        $this->user = $user;
        $this->restaurant = $restaurant;
        $this->dateSubmitted = new \DateTime();
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
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     *
     * @return $this
     */
    public function setState($state)
    {
        $this->state = $state;
        
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $total
     *
     * @return $this
     */
    public function setTotal($total)
    {
        $this->total = $total;
        
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
     *
     * @return $this
     */
    public function setRestaurant($restaurant)
    {
        $this->restaurant = $restaurant;
        
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $restaurant
     *
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     *
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;
        
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getDateCanceled()
    {
        return $this->dateCanceled;
    }

    /**
     * @param mixed $dateCanceled
     *
     * @return $this
     */
    public function setDateCanceled($dateCanceled)
    {
        $this->dateCanceled = $dateCanceled;
        
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateSubmitted()
    {
        return $this->dateSubmitted;
    }



    /**
     * @return mixed
     */
    public function getNbParticipants()
    {
        return $this->nbParticipants;
    }

    /**
     * @param mixed $paid
     *
     * @return $this
     */
    public function setNbParticipants($nbParticipants)
    {
        $this->nbParticipants = $nbParticipants;

        return $this;
    }

    /**
     * @return string
     */
    public function getTimeStep()
    {
        return $this->timeStep;
    }

    /**
     * @param string $timeStep
     */
    public function setTimeStep($timeStep)
    {
        $this->timeStep = $timeStep;
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param string $paymentMethod
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }






}

