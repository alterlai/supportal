<?php

namespace App\Repository;

use App\Entity\UserAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UserAction|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAction|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAction[]    findAll()
 * @method UserAction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAction::class);
    }

    /**
     * @return UserAction[]
     * SELECT user.username, COUNT(file_type), file_type
    FROM user_action
    INNER JOIN user ON user_action.user_id = user.id
    GROUP BY user_id, file_type
     */
    public function getGroupedByUser()
    {
        return $this->createQueryBuilder('action')
            ->join('action.user', 'user', 'WITH', 'action.user = user.id')
            ->select("user.username, COUNT(action.fileType) as total, action.fileType")
            ->groupBy('action.user, action.fileType')
            ->getQuery()
            ->getResult();
    }

    /**
     *
     */
    public function getGroupedByOrganisation()
    {

    }

    // /**
    //  * @return UserAction[] Returns an array of UserAction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserAction
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}