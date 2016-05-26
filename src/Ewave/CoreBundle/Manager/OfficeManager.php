<?php

namespace Ewave\CoreBundle\Manager;

use Ewave\CoreBundle\Entity\Environment;
use Ewave\CoreBundle\Entity\Office;
use Ewave\CoreBundle\Service\Coder;
use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @DI\Service("ewave.manager.office")
 */
class OfficeManager
{
    use Coder;
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
     * @param Office $office
     * @return bool
     */
    public function update($data, Office $office)
    {
        $data = $this->encodeArray($data);
        $office->setUser($data['user']);
        $office->setPassword($data['password']);
        $office->setUrl($data['url']);

        return $this->save($office);
    }

    /**
     * @param array $data
     * @param Environment $environment
     * @return bool
     */
    public function create($data, Environment $environment)
    {
        $office = new Office();
        $data = $this->encodeArray($data);
        $office->setUser($data['user']);
        $office->setPassword($data['password']);
        $office->setUrl($data['url']);
        $office->setEnvironment($environment);

        return $this->save($office);
    }

    /**
     * @param Office $office
     * @return bool
     */
    public function save(Office $office)
    {
        try {
            $this->entityManager->persist($office);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;
    }

    /**
     * @param Office $office
     * @return bool
     */
    public function delete(Office $office)
    {
        try {
            $this->entityManager->remove($office);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;

    }
}