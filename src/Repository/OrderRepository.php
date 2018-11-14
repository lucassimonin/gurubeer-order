<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * Class ContentRepository
 *
 * @package App\Repository
 */
class OrderRepository extends EntityRepository
{
    /**
     * @param $filters
     * @param User $user
     * @return \Doctrine\ORM\Query
     */
    public function queryForSearch($filters, User $user)
    {
        $qb = $this->createQueryBuilder('o')
           ->orderBy('o.updated', 'DESC');
        if (count($filters) > 0) {
            foreach ($filters as $key => $filter) {
                $qb->andWhere('o.'.$key.' LIKE :'.$key);
                $qb->setParameter($key, '%'.$filter.'%');
            }
        }
        if (in_array(User::ROLE_COMMERCIAL, $user->getRoles())) {
            $qb->andWhere('o.creator = :user')
                ->setParameter('user', $user);
        }

        return $qb->getQuery();
    }
}
