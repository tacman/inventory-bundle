<?php

namespace PlinioCardoso\InventoryBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PlinioCardoso\InventoryBundle\Entity\Stock;

/**
 * @extends ServiceEntityRepository<Stock>
 *
 * @method Stock|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stock|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stock[]    findAll()
 * @method Stock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    public function exists(array $criteria): bool
    {
        return (bool) $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->andWhere('e.product = :product AND e.location = :location')
            ->setParameter('product', $criteria['product'])
            ->setParameter('location', $criteria['location'])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function save(Stock $entity): Stock
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }
}
