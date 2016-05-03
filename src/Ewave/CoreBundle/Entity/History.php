<?php

namespace Ewave\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * History
 *
 * @ORM\Table(name="history", uniqueConstraints={
 *  @ORM\UniqueConstraint(columns={"id"})}, indexes={
 *  @ORM\Index(name="user_idx", columns={"user"})
 * })
 * @ORM\Entity(repositoryClass="Ewave\CoreBundle\Repository\HistoryRepository")
 */
class History
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="histories")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="params", type="string", length=5000)
     */
    private $params;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=500)
     */
    private $file;

    /**
     * @var integer
     *
     * @ORM\Column(name="cols", type="integer", length=10)
     */
    private $cols;


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
     * Set user
     *
     * @param \Ewave\CoreBundle\Entity\User $user
     * @return History
     */
    public function setUser(\Ewave\CoreBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Ewave\CoreBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set params
     *
     * @param string $params
     *
     * @return History
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get params
     *
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set date
     *
     * @param integer $date
     *
     * @return History
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set file
     *
     * @param string $file
     *
     * @return History
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set cols
     *
     * @param integer $file
     *
     * @return History
     */
    public function setCols($file)
    {
        $this->cols = $file;

        return $this;
    }

    /**
     * Get cols
     *
     * @return integer
     */
    public function getCols()
    {
        return $this->cols;
    }
}

