<?php

namespace Vilka\CoreBundle\Manager;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Vilka\CoreBundle\Entity\User;

/**
 * @DI\Service("vilka.manager.user")
 */
class UserManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     *
     * @DI\InjectParams({
     *      "entityManager"                = @DI\Inject("doctrine.orm.entity_manager")
     * })
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param array $data
     * @param User $user
     * @return bool
     */
    public function update(array $data, User $user)
    {
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        if ($data['password']) {
            $user->setPassword($user->setPlainPassword($data['password']));
        }
        return $this->save($user);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function save(User $user)
    {
        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;
    }
}