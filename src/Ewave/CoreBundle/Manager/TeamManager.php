<?php

namespace Ewave\CoreBundle\Manager;

use Ewave\CoreBundle\Entity\Team;
use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @DI\Service("ewave.manager.team")
 */
class TeamManager
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
     * @param Team $data
     * @param Team $team
     * @return bool
     */
    public function update(Team $data, Team $team)
    {
        $team->setTitle($data->getTitle());

        return $this->save($team);
    }

    /**
     * @param Team $team
     * @return bool
     */
    public function save(Team $team)
    {
        try {
            $this->entityManager->persist($team);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;
    }

    /**
     * @param Team $team
     * @return bool
     */
    public function delete(Team $team)
    {
        try {
            $this->entityManager->remove($team);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;

    }
}