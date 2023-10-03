<?php

namespace App\Repository;

use App\Entity\CVFileName;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CVFileName>
 *
 * @method CVFileName|null find($id, $lockMode = null, $lockVersion = null)
 * @method CVFileName|null findOneBy(array $criteria, array $orderBy = null)
 * @method CVFileName[]    findAll()
 * @method CVFileName[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CVFileNameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CVFileName::class);
    }

//    /**
//     * @return CVFileName[] Returns an array of CVFileName objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CVFileName
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
