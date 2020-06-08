<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\DocumentDraft;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DocumentDraft|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentDraft|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentDraft[]    findAll()
 * @method DocumentDraft[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentDraftRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentDraft::class);
    }

    public function getOpenDocumentDraftsByDocument(Document $document)
    {
        return $this->createQueryBuilder('draft')
            ->innerJoin('draft.draftStatus', 'status', 'WITH', 'status.id = draft.draftStatus')
            ->where('draft.document = :document')
            ->andWhere('status.name = :status')
            ->setParameter("document", $document)
            ->setParameter("status", "In behandeling")
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return DocumentDraft[] Returns an array of DocumentDraft objects
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
    public function findOneBySomeField($value): ?DocumentDraft
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
