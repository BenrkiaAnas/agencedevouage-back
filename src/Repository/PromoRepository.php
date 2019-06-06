<?php

namespace App\Repository;

use App\Entity\Promo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Promo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Promo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Promo[]    findAll()
 * @method Promo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Promo::class);
    }

    public function findByDateInterval($dtBegin, $dtEnd, $visible)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.visible = :visible')
            ->andWhere('p.dateBegin <= :valBegin')
            ->andWhere('p.dateEnd >= :valEnd')
            ->setParameter('visible', $visible)
            ->setParameter('valBegin', $dtBegin)
            ->setParameter('valEnd', $dtEnd)
            ->orderBy('p.dateBegin', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findPromoExpire($dtToday)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.dateEnd < :valdtToday')
            ->setParameter('valdtToday' , $dtToday)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Promo[] Returns an array of Promo objects
    //  */


    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Promo
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
