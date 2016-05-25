<?php

namespace Ewave\CoreBundle\Manager;

use Ewave\CoreBundle\Entity\Environment;
use Ewave\CoreBundle\Entity\Mysql;
use Ewave\CoreBundle\Service\Coder;
use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @DI\Service("ewave.manager.mysql")
 */
class MysqlManager
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
     * @param Mysql $mysql
     * @return bool
     */
    public function update($data, Mysql $mysql)
    {
        $data = $this->encodeArray($data);
        $mysql->setServer($data['server']);
        $mysql->setUser($data['user']);
        $mysql->setPort($data['port']);
        $mysql->setPassword($data['password']);
        $mysql->setDbname($data['dbname']);

        return $this->save($mysql);
    }

    /**
     * @param array $data
     * @param Environment $environment
     * @return bool
     */
    public function create($data, Environment $environment)
    {
        $ssh = new Mysql();
        $data = $this->encodeArray($data);
        $ssh->setServer($data['server']);
        $ssh->setUser($data['user']);
        $ssh->setPort($data['port']);
        $ssh->setPassword($data['password']);
        $ssh->setDbname($data['dbname']);
        $ssh->setEnvironment($environment);

        return $this->save($ssh);
    }

    /**
     * @param Mysql $mysql
     * @return bool
     */
    public function save(Mysql $mysql)
    {
        try {
            $this->entityManager->persist($mysql);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;
    }

    /**
     * @param Mysql $mysql
     * @return bool
     */
    public function delete(Mysql $mysql)
    {
        try {
            $this->entityManager->remove($mysql);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;

    }
}