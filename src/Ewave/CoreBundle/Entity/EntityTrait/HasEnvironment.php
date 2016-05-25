<?php

namespace Ewave\CoreBundle\Entity\EntityTrait;

use Doctrine\ORM\Mapping as ORM;
use Ewave\CoreBundle\Entity\Environment;

trait HasEnvironment
{

    /**
     * @ORM\ManyToOne(targetEntity="Environment", inversedBy="environment")
     * @ORM\JoinColumn(onDelete="CASCADE", name="environment", referencedColumnName="id")
     */
    private $environment;

    /**
     * Set environment
     *
     * @param Environment $environment
     * @return mixed
     */
    public function setEnvironment(Environment $environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * Get environment
     *
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }
}
