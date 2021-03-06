<?php

namespace Ewave\CoreBundle\Manager;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Ewave\CoreBundle\Entity\Team;
use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

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
    public function delete(Team $team, FlashBag $flashBag)
    {
        try {
            $this->entityManager->remove($team);
            $this->entityManager->flush();
        } catch (ForeignKeyConstraintViolationException $e) {
            $flashBag->add(
                'warning',
                'Please, delete all users from the team before'
            );
            return false;
        } catch (Exception $e) {

            return false;
        }

        return true;

    }
}