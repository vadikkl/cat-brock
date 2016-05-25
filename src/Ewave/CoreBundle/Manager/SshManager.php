<?php

namespace Ewave\CoreBundle\Manager;

use Ewave\CoreBundle\Entity\Environment;
use Ewave\CoreBundle\Entity\Ssh;
use Ewave\CoreBundle\Service\Coder;
use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @DI\Service("ewave.manager.ssh")
 */
class SshManager
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
     * @param Ssh $ssh
     * @return bool
     */
    public function update($data, Ssh $ssh)
    {
        $data = $this->encodeArray($data);
        $ssh->setServer($data['server']);
        $ssh->setUser($data['user']);
        $ssh->setPort($data['port']);
        $ssh->setPassword($data['password']);
        $ssh->setDescription($data['description']);

        return $this->save($ssh);
    }

    /**
     * @param array $data
     * @param Environment $environment
     * @return bool
     */
    public function create($data, Environment $environment)
    {
        $ssh = new Ssh();
        $data = $this->encodeArray($data);
        $ssh->setServer($data['server']);
        $ssh->setUser($data['user']);
        $ssh->setPort($data['port']);
        $ssh->setPassword($data['password']);
        $ssh->setDescription($data['description']);
        $ssh->setEnvironment($environment);

        return $this->save($ssh);
    }

    /**
     * @param Ssh $ssh
     * @return bool
     */
    public function save(Ssh $ssh)
    {
        try {
            $this->entityManager->persist($ssh);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;
    }

    /**
     * @param Ssh $ssh
     * @return bool
     */
    public function delete(Ssh $ssh)
    {
        try {
            $this->entityManager->remove($ssh);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;

    }
}