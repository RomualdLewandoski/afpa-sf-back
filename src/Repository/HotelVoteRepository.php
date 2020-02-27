<?php

namespace App\Repository;

use App\Entity\HotelVote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method HotelVote|null find($id, $lockMode = null, $lockVersion = null)
 * @method HotelVote|null findOneBy(array $criteria, array $orderBy = null)
 * @method HotelVote[]    findAll()
 * @method HotelVote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HotelVoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HotelVote::class);
    }

    // /**
    //  * @return HotelVote[] Returns an array of HotelVote objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HotelVote
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
