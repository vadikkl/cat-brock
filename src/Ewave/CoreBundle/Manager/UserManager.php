<?php

namespace Ewave\CoreBundle\Manager;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Ewave\CoreBundle\Entity\User;

/**
 * @DI\Service("ewave.manager.user")
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
     *      "entityManager" = @DI\Inject("doctrine.orm.entity_manager")
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
        $user->setEnabled((bool)$data['enabled']);
        if ($data['password']) {
            $user->setPassword($user->setPlainPassword($data['password']));
        }
        return $this->save($user);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function create($data)
    {
        $user = new User();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setPassword($user->setPlainPassword($data['password']));
        $user->setRoles(array("ROLE_USER"));
        $user->setEnabled((bool)$data['enabled']);
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

    /**
     * @param User $user
     * @return bool
     */
    public function delete(User $user)
    {
        try {
            $directory = getcwd() . "/files/" . md5($user->getId().$user->getSalt());
            system("rm -rf ".escapeshellarg($directory));
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;

    }
}