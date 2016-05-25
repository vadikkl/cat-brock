<?php

namespace Ewave\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Environment
 *
 * @ORM\Table("environments")
 * @ORM\Entity(repositoryClass="Ewave\CoreBundle\Repository\EnvironmentRepository")
 */
class Environment
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
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=true, length=10000)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="project")
     * @ORM\JoinColumn(onDelete="CASCADE", name="project", referencedColumnName="id")
     */
    private $project;

    /**
     * @ORM\OneToMany(targetEntity="Ssh", mappedBy="environment")
     */
    protected $sshs;

    /**
     * @ORM\OneToMany(targetEntity="Mysql", mappedBy="environment")
     */
    protected $mysqls;

    public function __construct()
    {
        $this->sshs = new ArrayCollection();
        $this->mysqls = new ArrayCollection();
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
     * Set type
     *
     * @param string $type
     *
     * @return Environment
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Environment
     */
    public function setDescription($description)
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
     * Set project
     *
     * @param Project $project
     * @return Environment
     */
    public function setProject(Project $project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Add ssh
     *
     * @param Ssh $ssh
     * @return Environment
     */
    public function addSsh(Ssh $ssh)
    {
        $this->sssh[] = $ssh;

        return $this;
    }

    /**
     * Remove ssh
     *
     * @param Ssh $ssh
     */
    public function removeSsh(Ssh $ssh)
    {
        $this->sshs->removeElement($ssh);
    }

    /**
     * Get ssh
     *
     * @return Collection
     */
    public function getSshs()
    {
        return $this->sshs;
    }

    /**
     * Add mysql
     *
     * @param Mysql $mysql
     * @return Environment
     */
    public function addMysql(Mysql $mysql)
    {
        $this->mysqls[] = $mysql;

        return $this;
    }

    /**
     * Remove ssh
     *
     * @param Mysql $mysql
     */
    public function removeMysql(Mysql $mysql)
    {
        $this->mysqls->removeElement($mysql);
    }

    /**
     * Get ssh
     *
     * @return Collection
     */
    public function getMysqls()
    {
        return $this->mysqls;
    }
}

