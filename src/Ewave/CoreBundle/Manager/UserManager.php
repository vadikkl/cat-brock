<?php

namespace Ewave\CoreBundle\Manager;

use Ewave\CoreBundle\Entity\Team;
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
     * @param Team $team
     * @return bool
     */
    public function update(array $data, User $user, Team $team)
    {
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setEnabled((bool)$data['enabled']);
        $user->setRoles($data['roles']);
        $user->setTeam($team);
        if ($data['password']) {
            $user->setPassword($user->setPlainPassword($data['password']));
        }
        return $this->save($user);
    }

    /**
     * @param array $data
     * @param Team $team
     * @return bool
     */
    public function create($data, Team $team)
    {
        $user = new User();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setPassword($user->setPlainPassword($data['password']));
        $user->setRoles($data['roles']);
        $user->setTeam($team);
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