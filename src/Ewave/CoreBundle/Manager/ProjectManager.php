<?php

namespace Ewave\CoreBundle\Manager;

use Ewave\CoreBundle\Entity\Project;
use Ewave\CoreBundle\Entity\Team;
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

        return $this->save($project);
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
        return $this->save($project);
    }

    /**
     * @param Project $project
     * @return bool
     */
    public function save(Project $project)
    {
        try {
            $this->entityManager->persist($project);
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
}