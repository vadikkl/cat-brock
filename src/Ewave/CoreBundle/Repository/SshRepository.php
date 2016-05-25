<?php

namespace Ewave\CoreBundle\Repository;

/**
 * SshRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SshRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAllByEnvironments($environments)
    {
        if (count($environments)) {
            $ids = array();
            foreach ($environments as $environment) {
                $ids[] = $environment->getId();
            }
            $query = $this->createQueryBuilder('s');
            $query->where('s.environment IN (:ids)')
                ->setParameter('ids', $ids);
            $query = $query->getQuery();

            return $query->getResult();
        }

        return array();
    }
}
