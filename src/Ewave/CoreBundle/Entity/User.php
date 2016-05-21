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

    /**
     *
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Project", inversedBy="users")
     */
    protected $projects;

    public function __construct()
    {
        parent::__construct();
        $this->projects = new ArrayCollection();
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
     * Get team
     *
     * @return \Ewave\CoreBundle\Entity\Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Add projects
     *
     * @param \Ewave\CoreBundle\Entity\Project $projects
     * @return User
     */
    public function addProject(\Ewave\CoreBundle\Entity\Project $projects)
    {
        $this->projects[] = $projects;

        return $this;
    }

    /**
     * Remove project
     *
     * @param \Ewave\CoreBundle\Entity\Project $projects
     */
    public function removeProject(\Ewave\CoreBundle\Entity\Project $projects)
    {
        $this->projects->removeElement($projects);
    }

    /**
     * Remove projects
     */
    public function removeAllProjects()
    {
        $this->projects = null;
    }

    /**
     * Get projects
     *
     * @return Project[]
     */
    public function getProjects()
    {
        return $this->projects;
    }
}
