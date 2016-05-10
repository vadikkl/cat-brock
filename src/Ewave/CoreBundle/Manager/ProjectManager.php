<?php

namespace Ewave\CoreBundle\Manager;

use Ewave\CoreBundle\Entity\Project;
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
     * @param Project $data
     * @param Project $project
     * @return bool
     */
    public function update(Project $data, Project $project)
    {
        $project->setTitle($data->getTitle());
        $project->setDescription($data->getDescription());

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