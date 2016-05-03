<?php

namespace Ewave\CoreBundle\Manager;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Ewave\CoreBundle\Entity\History;

/**
 * @DI\Service("ewave.manager.history")
 */
class HistoryManager
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
     * @param History $history
     * @return bool
     */
    public function save(History $history)
    {
        try {
            $this->entityManager->persist($history);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;
    }

    /**
     * @param History $history
     * @return bool
     */
    public function delete(History $history)
    {
        try {
            $this->entityManager->remove($history);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;

    }
}