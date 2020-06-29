<?php

namespace App\Repository;

use App\Entity\Discipline;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

use function Doctrine\ORM\QueryBuilder;

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
     * @return array
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

    /**
     * Find only whole numbers. These are the groups the user can filter.
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findGroups()
    {
        $em = $this->getEntityManager();

        $query = "SELECT id, code, description FROM discipline WHERE FLOOR(discipline.code) = discipline.code ";

        $statement = $em->getConnection()->prepare($query);

        $statement->execute();

        $disciplines =  $statement->fetchAll();

        $result = array();
        $currentGroupIndex = 0;

        foreach ($disciplines as $discipline)
        {
            if(ctype_digit((string)$discipline['code']) && $discipline['code'] < 10)
            {
                $currentGroupIndex++;
            }
            $result[$currentGroupIndex][] = $discipline;
        }

        return $result;
    }
}
