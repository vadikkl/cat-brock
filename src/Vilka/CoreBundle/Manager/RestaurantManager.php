<?php

namespace Vilka\CoreBundle\Manager;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Vilka\CoreBundle\Entity\Restaurant;

/**
 * @DI\Service("vilka.manager.restaurant")
 */
class RestaurantManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     *
     * @DI\InjectParams({
     *      "entityManager"                = @DI\Inject("doctrine.orm.entity_manager")
     * })
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Restaurant $restaurant
     */
    public function update(Restaurant $restaurant)
    {
        $this->entityManager->flush($restaurant);
    }
}