<?php

namespace Vilka\CoreBundle\Manager;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Vilka\CoreBundle\Entity\Subway;

/**
 * @DI\Service("vilka.manager.subway")
 */
class SubwayManager
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
     * @param Subway $data
     * @param Subway $subway
     * @return bool
     */
    public function update(Subway $data, Subway $subway)
    {
        $subway->setLine($data->getLine());
        $subway->setName($data->getName());
        return $this->save($subway);
    }

    /**
     * @param Subway $subway
     * @return bool
     */
    public function save(Subway $subway)
    {
        try {
            $this->entityManager->persist($subway);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;
    }

    /**
     * @param Subway $subway
     */
    public function delete(Subway $subway)
    {
        try {
            $this->entityManager->remove($subway);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;

    }
}