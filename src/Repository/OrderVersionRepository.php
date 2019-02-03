<?php

namespace App\Repository;

use App\Entity\OrderVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class OrderVersionRepository
 *
 * @package App\Repository
 */
class OrderVersionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OrderVersion::class);
    }

    /**
     * @param int $orderId
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLastVersion(int $orderId)
    {
        $sql = "SELECT ov.* " .
            "FROM gurubeer_order_version ov " .
            "INNER JOIN ( SELECT max(ov2.version) AS maxVersion FROM gurubeer_order_version ov2 WHERE ov2.order_id = :id ) groupov " .
            "INNER JOIN gurubeer_order o ON ov.order_id = o.id " .
            "WHERE ov.version = groupov.maxVersion " .
            "AND o.id = :idex";
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(OrderVersion::class, "ov");
        $stmt = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $stmt->setParameter(":id", $orderId);
        $stmt->setParameter(":idex", $orderId);
        $stmt->execute();

        return $stmt->getOneOrNullResult();
    }

    /**
     * @param int $orderId
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getBeforeVersion(int $orderId)
    {
        $sql = "SELECT ov.* " .
            "FROM gurubeer_order_version ov " .
            "INNER JOIN ( SELECT min(ov2.version) AS minVersion FROM gurubeer_order_version ov2 WHERE ov2.order_id = :id ) groupov " .
            "INNER JOIN gurubeer_order o ON ov.order_id = o.id " .
            "WHERE ov.version = groupov.minVersion " .
            "AND o.id = :idex";
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(OrderVersion::class, "ov");
        $stmt = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $stmt->setParameter(":id", $orderId);
        $stmt->setParameter(":idex", $orderId);
        $stmt->execute();

        return $stmt->getOneOrNullResult();
    }

    /**
     * @param int $orderId
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMinVersion(int $orderId)
    {
        return intval($this->createQueryBuilder('v')
            ->select('min(v.version)')
            ->where('v.order = :id')
            ->setParameter('id', $orderId)
            ->getQuery()
            ->getSingleScalarResult())
        ;
    }
}
