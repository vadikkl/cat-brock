<?php

namespace Vilka\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class UserRepository extends EntityRepository
{
    protected $_roleAdmin = 'ROLE_ADMIN';

    public function getList(
        $search,
        $page = 1,
        $limit = 20
    ) {
        $query = $this->createQueryBuilder('u')
            ->addSelect('u');
        $order = isset($_COOKIE['sort_table']) ? explode(' ', $_COOKIE['sort_table']) : array('id', 'desc');
        $this->setFilters($query, $search);
        $query = $query->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
            ->orderBy('u.'.$order[0], strtoupper($order[1]))
            ->getQuery();

        return $query->getResult();
    }

    public function getListPages(
        $search,
        $limit = 20
    ) {
        $query = $this->createQueryBuilder('u')
            ->select('COUNT(u)');
        $this->setFilters($query, $search);
        $pager['limit'] = $limit;
        $pager['count'] = $query->getQuery()->getSingleScalarResult();

        return $pager;
    }

    public function count() {
        $query = $this->createQueryBuilder('u')
            ->select('COUNT(u)');
        $query->andWhere('u.roles NOT LIKE :role')
            ->setParameter('role', '%' . $this->_roleAdmin . '%');

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * @param QueryBuilder $query
     * @param string $search
     */
    private function setFilters(&$query, $search)
    {
        if ($search) {
            $query->Where('u.username LIKE :search')
                ->setParameter('search', '%' . $search . '%');
            $query->orWhere('u.email LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
        $query->andWhere('u.roles NOT LIKE :role')
            ->setParameter('role', '%' . $this->_roleAdmin . '%');
    }
}
