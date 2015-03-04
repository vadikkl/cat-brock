<?php

namespace Vilka\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Vilka\CoreBundle\Repository\UserRepository")
 */
class User extends BaseUser
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Restaurant", inversedBy="users")
     */
    //protected $restaurants;

    /*public function __construct()
    {
        parent::__construct();
        $this->restaurants = new ArrayCollection();
    }*/

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
     * Add restaurants
     *
     * @param \Vilka\CoreBundle\Entity\Restaurant $restaurants
     * @return User
     */
    /*public function addRestaurant(\Vilka\CoreBundle\Entity\Restaurant $restaurants)
    {
        $this->restaurants[] = $restaurants;

        return $this;
    }*/

    /**
     * Remove restaurants
     *
     * @param \Vilka\CoreBundle\Entity\Restaurant $restaurants
     */
    /*public function removeRestaurant(\Vilka\CoreBundle\Entity\Restaurant $restaurants)
    {
        $this->restaurants->removeElement($restaurants);
    }*/

    /**
     * Get restaurants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    /*public function getRestaurants()
    {
        return $this->restaurants;
    }*/
}
