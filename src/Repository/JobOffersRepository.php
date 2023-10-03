<?php

namespace App\Repository;

use App\Entity\JobOffers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JobOffers>
 *
 * @method JobOffers|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobOffers|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobOffers[]    findAll()
 * @method JobOffers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobOffersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobOffers::class);
    }

//    /**
//     * @return JobOffers[] Returns an array of JobOffers objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?JobOffers
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
