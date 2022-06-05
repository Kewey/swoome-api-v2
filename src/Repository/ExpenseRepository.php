<?php

namespace App\Repository;

use App\Entity\Expense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Expense|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expense|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expense[]    findAll()
 * @method Expense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }


    public function findExpenseByUserAndGroup($user, $group)
    {
        $qb = $this->createQueryBuilder('e');
        return $qb
            ->where('e.expenseGroup = :group')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isMemberOf(':user', 'e.participants'),
                $qb->expr()->eq(':user', 'e.madeBy'),
            ))
            ->setParameter('group', $group)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Expense
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
