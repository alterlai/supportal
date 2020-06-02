<?php

namespace App\Repository;

use App\Entity\DraftStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DraftStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method DraftStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method DraftStatus[]    findAll()
 * @method DraftStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DraftStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DraftStatus::class);
    }

    // /**
    //  * @return DraftStatus[] Returns an array of DraftStatus objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DraftStatus
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
