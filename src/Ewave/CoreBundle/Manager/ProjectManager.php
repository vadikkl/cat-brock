<?php

namespace Ewave\CoreBundle\Manager;

use Ewave\CoreBundle\Entity\Project;
use Ewave\CoreBundle\Entity\Team;
use Ewave\CoreBundle\Entity\User;
use Ewave\CoreBundle\Repository\UserRepository;
use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @DI\Service("ewave.manager.project")
 */
class ProjectManager
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
     * @param $data
     * @param Project $project
     * @param Team $team
     * @return bool
     */
    public function update($data, Project $project, Team $team = null)
    {
        $project->setTitle($data['title']);
        $project->setDescription($data['description']);
        $project->setTeam($team);
        $this->removeAllUsers($project);

        return $this->save($project, $data);
    }

    /**
     * @param array $data
     * @param Team $team
     * @return bool
     */
    public function create($data, Team $team = null)
    {
        $project = new Project();
        $project->setTitle($data['title']);
        $project->setDescription($data['description']);
        $project->setTeam($team);

        return $this->save($project, $data);
    }

    /**
     * @param Project $project
     * @return bool
     */
    public function save(Project $project, $data)
    {
        try {
            $this->entityManager->persist($project);
            $this->entityManager->flush();

            if (count($data['users'])) {
                $users = $this->_getUserRepository()->getByIds($data['users']);
                foreach ($users as $user) {
                    $user->addProject($project);
                    $this->saveUser($user);
                }
            }
        } catch (Exception $e) {

            return false;
        }

        return true;
    }

    public function saveUser(User $user)
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
     * @param Project $project
     * @return bool
     */
    public function delete(Project $project)
    {
        try {
            $this->entityManager->remove($project);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;
    }

    /**
     * @return UserRepository
     */
    private function _getUserRepository()
    {
        return $this->entityManager->getRepository('EwaveCoreBundle:User');
    }

    /**
     * Remove users
     * @param Project $project
     */
    public function removeAllUsers($project)
    {
        foreach ($project->getUsers() as $user) {
            $user->removeProject($project);
            $this->saveUser($user);
        }
    }
}