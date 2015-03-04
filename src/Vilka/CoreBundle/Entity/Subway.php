<?php

namespace Vilka\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Subway
 *
 * @ORM\Table(name="subway")
 * @ORM\Entity(repositoryClass="Vilka\CoreBundle\Repository\SubwayRepository")
 */
class Subway
{
    const LINE_BLUE = 'Московская';
    const LINE_RED = 'Автозаводская';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank(message = "Поле не может быть пустым")
     *
     * @Assert\Choice(
     *     choices = { "Московская", "Автозаводская" },
     *     message = "Данная ветка недоступна для выбора"
     * )
     * @ORM\Column(name="line", type="string", length=255)
     */
    private $line;

    /**
     * @var ArrayCollection|null
     *
     * @ORM\ManyToMany(targetEntity="Restaurant", mappedBy="subways")
     * @ORM\JoinColumn(name="subway_id", referencedColumnName="id", nullable=true)
     */
    private $restaurants;

    public function __construct()
    {
        //$this->restaurants = new ArrayCollection();
    }

    /**
     * List of subway lines
     *
     */
    public static function getSubwayLine($key = NULL)
    {
        $items = array(
            self::LINE_BLUE => 'Московская',
            self::LINE_RED => 'Автозаводская'
        );

        if($key !== NULL && isset($items[$key]))
            return $items[$key];
        return $items;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Subway
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add restaurants
     *
     * @param \Vilka\CoreBundle\Entity\Restaurant $restaurants|null
     * @return Subway
     */
    public function addRestaurant(\Vilka\CoreBundle\Entity\Restaurant $restaurants = null)
    {
        $this->restaurants[] = $restaurants;

        return $this;
    }

    /**
     * Remove restaurants
     *
     * @param \Vilka\CoreBundle\Entity\Restaurant $restaurants
     */
    public function removeRestaurant(\Vilka\CoreBundle\Entity\Restaurant $restaurants)
    {
        $this->restaurants->removeElement($restaurants);
    }

    /**
     * Get restaurants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRestaurants()
    {
        return $this->restaurants;
    }

    /**
     * Set line
     *
     * @param string $line
     *
     * @return Subway
     */
    public function setLine($line)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * Get line
     *
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }
}