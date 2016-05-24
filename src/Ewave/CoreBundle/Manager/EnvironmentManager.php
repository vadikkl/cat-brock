<?php

namespace Ewave\CoreBundle\Manager;

use Ewave\CoreBundle\Entity\Environment;
use Ewave\CoreBundle\Entity\Project;
use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @DI\Service("ewave.manager.environment")
 */
class EnvironmentManager
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
     * @param Environment $environment
     * @return bool
     */
    public function update($data, Environment $environment)
    {
        $environment->setType($data['type']);
        $environment->setDescription($data['description']);

        return $this->save($environment);
    }

    /**
     * @param array $data
     * @param Project $project
     * @return bool
     */
    public function create($data, Project $project)
    {
        $environment = new Environment();
        $environment->setType($data['type']);
        $environment->setDescription($data['description']);
        $environment->setProject($project);

        return $this->save($environment);
    }

    /**
     * @param Environment $environment
     * @return bool
     */
    public function save(Environment $environment)
    {
        try {
            $this->entityManager->persist($environment);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;
    }

    /**
     * @param Environment $environment
     * @return bool
     */
    public function delete(Environment $environment)
    {
        try {
            $this->entityManager->remove($environment);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;

    }
}