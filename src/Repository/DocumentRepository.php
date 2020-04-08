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

    public function findWithFilter(User $user, array $disciplines = null, int $floor = null, arrray $buildings)
    {
        // Get documents related to the current user
        $query = $this->createQueryBuilder('d')
            ->innerJoin('d.location', 'l', 'WITH', 'd.location = l.id')
            ->innerJoin('l.organisation_id', 'o', 'WITH',  'l.organisation_id = o.id')
            ->innerJoin('user', 'u', 'WITH', 'u.organisation_id = o.id')
            ->where('u.id = :userid')
            ->setParameter(':userid', $user->getId());

        if ($disciplines)
        {
            for ($i = 0; $i < count($disciplines); $i++)
                $query->orWhere(" ")
                    ->setParameter(
                    sprintf('discipline_filter%s',$i) , $disciplines[$i-1]
            );
        }
    }

}
