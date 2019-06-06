<?php

namespace App\Repository;

use App\Entity\Inclusion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Inclusion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inclusion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inclusion[]    findAll()
 * @method Inclusion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InclusionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Inclusion::class);
    }

    // /**
    //  * @return Inclusion[] Returns an array of Inclusion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Inclusion
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
