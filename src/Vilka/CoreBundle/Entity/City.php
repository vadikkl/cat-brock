<?php

namespace Vilka\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * City
 *
 * @ORM\Table(name="city")
 * @ORM\Entity(repositoryClass="Vilka\CoreBundle\Repository\CityRepository")
 */
class City
{
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="District", mappedBy="city")
     */
    protected $districts;

    /**
     * @ORM\OneToMany(targetEntity="Restaurant", mappedBy="city")
     */
    protected $restaurants;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->districts = new ArrayCollection();
        $this->restaurants = new ArrayCollection();
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
     * @return City
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
     * Add districts
     *
     * @param \Vilka\CoreBundle\Entity\District $districts
     * @return City
     */
    public function addDistrict(\Vilka\CoreBundle\Entity\District $districts)
    {
        $this->districts[] = $districts;

        return $this;
    }

    /**
     * Remove districts
     *
     * @param \Vilka\CoreBundle\Entity\District $districts
     */
    public function removeDistrict(\Vilka\CoreBundle\Entity\District $districts)
    {
        $this->districts->removeElement($districts);
    }

    /**
     * Get districts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDistricts()
    {
        return $this->districts;
    }

    /**
     * Add restaurants
     *
     * @param \Vilka\CoreBundle\Entity\Restaurant $restaurants
     * @return City
     */
    public function addRestaurant(\Vilka\CoreBundle\Entity\Restaurant $restaurants)
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
}
