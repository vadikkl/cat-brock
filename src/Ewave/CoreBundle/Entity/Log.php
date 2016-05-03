<?php

namespace Ewave\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Log
 *
 * @ORM\Table("log", uniqueConstraints={
 *  @ORM\UniqueConstraint(columns={"platform"})
 * })
 * @ORM\Entity(repositoryClass="Ewave\CoreBundle\Repository\LogRepository")
 */
class Log
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
     * @ORM\Column(name="platform", type="string", length=255)
     */
    private $platform;

    /**
     * @var string
     *
     * @ORM\Column(name="params", type="string", length=500)
     */
    private $params;


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
     * Set platform
     *
     * @param string $platform
     *
     * @return Log
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * Get platform
     *
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Set params
     *
     * @param string $params
     *
     * @return Log
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
}

