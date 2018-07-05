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
     * @ORM\Column(type="boolean")
     */
    protected $state;

    /**
     * @ORM\Column(type="decimal", scale=2)
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
     * @ORM\Column(name="date_canceled", type="datetime", nullable=true)
     */
    protected $dateCanceled;

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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Content")
     * @ORM\JoinTable(name="reservation_contents",
     *      joinColumns={@ORM\JoinColumn(name="content_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="reservation_id", referencedColumnName="id")}
     *      )
     */
    protected $contents;

    public function __construct(User $user, Restaurant $restaurant){
        $this->user = $user;
        $this->restaurant = $restaurant;
        $this->state = self::STATE_PAID;
        $this->contents = new ArrayCollection();
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
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     *
     * @return $this
     */
    public function setClient($client)
    {
        $this->client = $client;
        
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
     * @return ArrayCollection
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @param ArrayCollection $contents
     *
     * @return $this
     */
    public function setContents($contents)
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * @param Content $content
     *
     * @return $this
     */
    public function addContent($content)
    {
        if (!$this->contents->contains($content)) {
            $this->contents->add($content);
        }

        return $this;
    }

    /**
     * @param Content $content
     *
     * @return $this
     */
    public function removeContent($content)
    {
        if ($this->contents->contains($content)) {
            $this->contents->remove($content);
        }

        return $this;
    }
}

