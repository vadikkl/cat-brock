<?php

namespace Ewave\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Ewave\CoreBundle\Repository\UserRepository")
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
     * @ORM\ManyToMany(targetEntity="History", inversedBy="users")
     */
    protected $histories;

    public function __construct()
    {
        parent::__construct();
        $this->histories = new ArrayCollection();
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
     * Add histories
     *
     * @param \Ewave\CoreBundle\Entity\History $histories
     * @return User
     */
    public function addHistory(\Ewave\CoreBundle\Entity\History $histories)
    {
        $this->histories[] = $histories;

        return $this;
    }

    /**
     * Remove histories
     *
     * @param \Ewave\CoreBundle\Entity\History $histories
     */
    public function removeHistory(\Ewave\CoreBundle\Entity\History $histories)
    {
        $this->histories->removeElement($histories);
    }

    /**
     * Get histories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHistories()
    {
        return $this->histories;
    }

}
