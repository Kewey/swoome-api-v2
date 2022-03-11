<?php

namespace App\Repository;

use App\Entity\GroupType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GroupType|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupType|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupType[]    findAll()
 * @method GroupType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupType::class);
    }

    // /**
    //  * @return GroupType[] Returns an array of GroupType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GroupType
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
