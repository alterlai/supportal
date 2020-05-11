<?php

namespace App\Repository;

use App\Entity\ViewHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ViewHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ViewHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ViewHistory[]    findAll()
 * @method ViewHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ViewHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ViewHistory::class);
    }

    // /**
    //  * @return ViewHistory[] Returns an array of ViewHistory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ViewHistory
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
