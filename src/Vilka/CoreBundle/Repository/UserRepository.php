<?php

namespace Vilka\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
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

    private function setFilters(&$query, $search)
    {
        if ($search) {
            $query = $query->where('u.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
            $query = $query->orWhere('u.value LIKE :search')
                ->setParameter('search', '%' . $search . '%');
            $query = $query->orWhere('u.description LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
    }
}
