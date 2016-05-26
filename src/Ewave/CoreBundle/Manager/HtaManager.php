<?php

namespace Ewave\CoreBundle\Manager;

use Ewave\CoreBundle\Entity\Environment;
use Ewave\CoreBundle\Entity\Hta;
use Ewave\CoreBundle\Service\Coder;
use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @DI\Service("ewave.manager.hta")
 */
class HtaManager
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
     * @param Hta $hta
     * @return bool
     */
    public function update($data, Hta $hta)
    {
        $data = $this->encodeArray($data);
        $hta->setUser($data['user']);
        $hta->setPassword($data['password']);

        return $this->save($hta);
    }

    /**
     * @param array $data
     * @param Environment $environment
     * @return bool
     */
    public function create($data, Environment $environment)
    {
        $hta = new Hta();
        $data = $this->encodeArray($data);
        $hta->setUser($data['user']);
        $hta->setPassword($data['password']);
        $hta->setEnvironment($environment);

        return $this->save($hta);
    }

    /**
     * @param Hta $hta
     * @return bool
     */
    public function save(Hta $hta)
    {
        try {
            $this->entityManager->persist($hta);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;
    }

    /**
     * @param Hta $hta
     * @return bool
     */
    public function delete(Hta $hta)
    {
        try {
            $this->entityManager->remove($hta);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;

    }
}