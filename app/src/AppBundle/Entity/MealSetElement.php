<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 * @ORM\Entity
 * @ORM\Table(name="meal_set_element")
 */
class MealSetElement
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
     * @ORM\ManyToOne(targetEntity="Content")
     * @ORM\JoinColumn(name="content_id")
     */
    protected $content;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MealSet")
     * @ORM\JoinColumn(name="meal_set_id", referencedColumnName="id")
     */
    protected $mealSet;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer")
     */
    protected $type;



    const TYPE_STARTER = 1;
    const TYPE_DISH = 2;
    const TYPE_SIDE = 3;
    const TYPE_DRINK = 4;
    const TYPE_DESSERT = 5;


    public static $types = array(
        self::TYPE_STARTER => 'starter',
        self::TYPE_DISH => 'dish',
        self::TYPE_SIDE => 'side',
        self::TYPE_DRINK => 'drink',
        self::TYPE_DESSERT => 'dessert'
    );

    public function __construct()
    {
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
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $contents
     */
    public function setContent($content)
    {
        $this->content = $content;
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
     * @return mixed
     */
    public function getMealSet()
    {
        return $this->mealSet;
    }

    /**
     * @param mixed $mealSet
     */
    public function setMealSet($mealSet)
    {
        $this->mealSet = $mealSet;
    }






}