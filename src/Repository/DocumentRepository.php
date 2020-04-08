<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\Location;
use App\Entity\Organisation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    /**
     * Find documents in a specific discipline range.
     * @param float $start
     * @param float $end
     * @return Document[] Returns an array of Document objects
     */
    public function findByDisciplineRange(float $start, float $end)
    {
        return $this->createQueryBuilder('d')
            ->join('d.discipline', 'dp', 'WITH', 'd.discipline = dp.id')
            ->andWhere('dp.code BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Search for documents in all columns.
     * @param string $inputString
     * @return Document[] Returns array of Document objects
     */
    public function searchAllColumns(string $inputString) {
        return $this->createQueryBuilder('d')
            ->join('d.discipline', 'dp', 'WITH', 'd.discipline = dp.id')
            ->where('d.id LIKE :input')
            ->orWhere('dp.code LIKE :input')
            ->orWhere('dp.description LIKE :input')
            ->orWhere('d.description LIKE :input')
            ->orWhere('d.file_name LIKE :input')
            ->orWhere('d.updated_at LIKE :input')
            ->setParameter('input', '%'.$inputString.'%')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Find all documents related to the current user and organisation
     * @param UserInterface $user
     * @return array|Document
     */
    public function findByCurrentUser(UserInterface $user)
    {
        return $this->createQueryBuilder('d')
            ->innerJoin('d.location', 'l', 'WITH', 'd.location = l.id')
            ->innerJoin('l.organisation_id', 'o', 'WITH',  'l.organisation_id = o.id')
            ->innerJoin('o.users', 'u', 'WITH', 'u.organisation = o.id')
            ->where('u.id = :userid')
            ->setParameter(':userid', $user->getId())
            ->getQuery()
            ->getResult();
    }
    /*
    public function findOneBySomeField($value): ?Document
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findWithFilter(UserInterface $user, array $disciplines = null, int $floor = null, array $buildings = null)
    {

        // Get documents related to the current user
        $query = $this->createQueryBuilder('d')
            ->innerJoin('d.location', 'l', 'WITH', 'd.location = l.id')
            ->join('d.discipline', 'dp', 'WITH', 'd.discipline = dp.id')
            ->innerJoin('l.organisation_id', 'o', 'WITH',  'l.organisation_id = o.id')
            ->innerJoin('o.users', 'u', 'WITH', 'u.organisation = o.id');


        // If disciplines filter is set, apply filter
        if ($disciplines)
        {
            foreach($disciplines as $i => $discipline)
            {
                $query->orWhere("dp.code LIKE :discipline$i")
                    ->setParameter("discipline$i", $discipline."%");
            }
        }

        // Filter on floor level
        if ($floor)
        {
            $query->andWhere("d.floor = :floor")
                ->setParameter("floor", $floor);
        }

        // Match the values in $buildings array with the first 4 characters of the filename.
        if ($buildings)
        {
            $query->andWhere($query->expr()->in($query->expr()->substring("d.file_name", 1, 4), $buildings));
        }

        // And filter on User id
        return $query->andWhere('u.id = :userid')
            ->setParameter(':userid', $user->getId())
            ->getQuery()
            ->getResult();
    }

}
