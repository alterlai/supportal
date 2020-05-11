<?php

namespace App\Repository;

use App\Entity\Discipline;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Discipline|null find($id, $lockMode = null, $lockVersion = null)
 * @method Discipline|null findOneBy(array $criteria, array $orderBy = null)
 * @method Discipline[]    findAll()
 * @method Discipline[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisciplineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discipline::class);
    }

    /**
     * Return all document types in a hierarchical array
     * @Return array
     * TODO: verwijderen zodra design gekozen is.
     */
    public function findAllAsGroupedArray()
    {
        $disciplines = $this->findAll();

        $result = array();
        $currentGroupIndex = 0;

        foreach ($disciplines as $discipline)
        {
            if(ctype_digit((string)$discipline->getCode()) && $discipline->getCode() < 10)
            {
                $currentGroupIndex++;
            }
            $result[$currentGroupIndex][] = $discipline;
        }
        return $result;
    }
}
