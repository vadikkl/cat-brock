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
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="teams")
     * @ORM\JoinColumn(name="team", referencedColumnName="id")
     */
    private $team;

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
     * Set team
     *
     * @param \Ewave\CoreBundle\Entity\Team $team
     * @return History
     */
    public function setTeam(\Ewave\CoreBundle\Entity\Team $team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Ewave\CoreBundle\Entity\Team
     */
    public function getTeam()
    {
        return $this->team;
    }
}
