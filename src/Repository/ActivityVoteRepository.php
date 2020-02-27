<?php

namespace App\Repository;

use App\Entity\ActivityVote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ActivityVote|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivityVote|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivityVote[]    findAll()
 * @method ActivityVote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityVoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityVote::class);
    }

    // /**
    //  * @return ActivityVote[] Returns an array of ActivityVote objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ActivityVote
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
