<?php

namespace Vilka\CoreBundle\Manager;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Vilka\CoreBundle\Entity\Setting;

/**
 * @DI\Service("vilka.manager.setting")
 */
class SettingManager
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
     * @param Setting $data
     * @param Setting $setting
     * @return bool
     */
    public function update(Setting $data, Setting $setting)
    {
        $setting->setValue($data->getValue());
        $setting->setName($data->getName());
        $setting->setDescription($data->getDescription());

        return $this->save($setting);
    }

    /**
     * @param Setting $setting
     * @return bool
     */
    public function save(Setting $setting)
    {
        try {
            $this->entityManager->persist($setting);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;
    }

    /**
     * @param Setting $setting
     * @return bool
     */
    public function delete(Setting $setting)
    {
        try {
            $this->entityManager->remove($setting);
            $this->entityManager->flush();
        } catch (Exception $e) {

            return false;
        }

        return true;

    }
}