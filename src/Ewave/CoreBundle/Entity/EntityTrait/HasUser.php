<?php

namespace Ewave\CoreBundle\Entity\EntityTrait;

use Doctrine\ORM\Mapping as ORM;
use Ewave\CoreBundle\Entity\User;

trait HasUser {
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $user;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @var User $user
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

}
