<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class ContentRepository
 *
 * @package App\Repository
 */
class OrderRepository extends EntityRepository
{
    /**
     * @param array $filters
     * @return mixed
     */
    public function queryForSearch($filters = array())
    {
        $qb = $this->createQueryBuilder('o')
           ->orderBy('o.updated', 'DESC');
        if (count($filters) > 0) {
            foreach ($filters as $key => $filter) {
                $qb->andWhere('o.'.$key.' LIKE :'.$key);
                $qb->setParameter($key, '%'.$filter.'%');
            }
        }

        return $qb->getQuery();
    }
}
