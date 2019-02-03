<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use App\Model\SearchInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class OrderRepository
 *
 * @package App\Repository
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Order::class);
    }
    /**
     * @param SearchInterface $search
     * @param User $user
     * @return \Doctrine\ORM\Query
     */
    public function queryForSearch(SearchInterface $search, User $user)
    {
        $filters = $search->getFilters();
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
