<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 * @ORM\Entity
 * @ORM\Table(name="mealSetElement")
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
     * @var integer
     *
     * @ORM\Column(name="mealSetType", type="integer")
     */
    protected $mealSetType;



    const MEALSETTYPE_STARTER = 1;
    const MEALSETTYPE_DISH = 2;
    const MEALSETTYPE_SIDE = 3;
    const MEALSETTYPE_DRINK = 4;
    const MEALSETTYPE_DESSERT = 5;


    public static $mealSetTypes = array(
        self::MEALSETTYPE_STARTER => 'starter',
        self::MEALSETTYPE_DISH => 'dish',
        self::MEALSETTYPE_SIDE => 'side',
        self::MEALSETTYPE_DRINK => 'drink',
        self::MEALSETTYPE_DESSERT => 'dessert'
    );

    public function __construct()
    {
    }



    /**
     * @return int
     */
    public function getMealSetType()
    {
        return $this->mealSetType;
    }

    /**
     * @param int $mealSetType
     */
    public function setMealSetType($mealSetType)
    {
        $this->mealSetType = $mealSetType;
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




}