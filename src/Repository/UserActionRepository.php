<?php

namespace App\Repository;

use App\Entity\User;
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
     */
    public function getGroupedByUser(\DateTime $from, \DateTime $to)
    {
        return $this->createQueryBuilder('action')
            ->innerJoin('action.user', 'user', 'WITH', 'action.user = user.id')
            ->innerJoin("user.organisation", 'o', 'WITH', 'user.organisation = o.id')
            ->select("user.username as username")
            ->addSelect("COUNT(action.fileType) as total")
            ->addSelect("action.fileType")
            ->addSelect("SUM(DATE_DIFF(action.deadline, action.downloadedAt)) as gereserveerd")
            ->addSelect("SUM(DATE_DIFF(action.returnedAt, action.downloadedAt)) as uitgeleend")
            ->addSelect("o.name as organisation")
            ->where("action.downloadedAt BETWEEN :from AND :to")
            ->groupBy('action.user, action.fileType')
            ->setParameter("from", $from)
            ->setParameter("to", $to)
            ->getQuery()
            ->getResult();
    }

    /**
     *  Get User actions corresponding to a single user.
     * @param User $user
     * @param \DateTime $from
     * @param \DateTime $to
     * @return mixed
     */
    public function getFromUser(User $user, \DateTime $from, \DateTime $to)
    {
        return $this->createQueryBuilder('action')
            ->innerJoin('action.user', 'user', 'WITH', 'action.user = user.id')
            ->innerJoin("user.organisation", 'o', 'WITH', 'user.organisation = o.id')
            ->select("user.username as username")
            ->addSelect("COUNT(action.fileType) as downloads")
            ->addSelect("action.fileType")
            ->addSelect("SUM(DATE_DIFF(action.deadline, action.downloadedAt)) as gereserveerd")
            ->addSelect("SUM(DATE_DIFF(action.returnedAt, action.downloadedAt)) as uitgeleend")
            ->addSelect("o.name as organisation")
            ->where("action.downloadedAt BETWEEN :from AND :to")
            ->andWhere("user.id = :userId")
            ->groupBy('action.fileType')
            ->setParameter("from", $from)
            ->setParameter("to", $to)
            ->setParameter("userId", $user->getId())
            ->getQuery()
            ->getResult();
    }


    public function getGroupedByOrganisation()
    {
        // TODO
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
