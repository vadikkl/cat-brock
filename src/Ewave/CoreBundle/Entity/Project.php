<?php

namespace Ewave\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table("project")
 * @ORM\Entity(repositoryClass="Ewave\CoreBundle\Repository\ProjectRepository")
 */
class Project
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=true, length=5000)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="teams")
     * @ORM\JoinColumn(onDelete="SET NULL", name="team", referencedColumnName="id")
     */
    private $team;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="projects")
     */
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity="Environment", mappedBy="project")
     */
    protected $environments;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->environments = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return Project
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Project
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set team
     *
     * @param \Ewave\CoreBundle\Entity\Team $team
     * @return Project
     */
    public function setTeam(\Ewave\CoreBundle\Entity\Team $team = null)
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
     * Add users
     *
     * @param \Ewave\CoreBundle\Entity\User $users
     * @return User
     */
    public function addUser(\Ewave\CoreBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Ewave\CoreBundle\Entity\User $user
     */
    public function removeUser(\Ewave\CoreBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add Environment
     *
     * @param Environment $environment
     * @return Project
     */
    public function addEnvironment(Environment $environment)
    {
        $this->environments[] = $environment;

        return $this;
    }

    /**
     * Remove Environment
     *
     * @param Environment $environment
     */
    public function removeEnvironment(Environment $environment)
    {
        $this->environments->removeElement($environment);
    }

    /**
     * Get Environment
     *
     * @return Collection
     */
    public function getEnvironments()
    {
        return $this->environments;
    }
}

