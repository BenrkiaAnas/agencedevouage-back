<?php

namespace App\Repository;

use App\Entity\Inclusion;
use App\Entity\PlanningVoyage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PlanningVoyage|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanningVoyage|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanningVoyage[]    findAll()
 * @method PlanningVoyage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanningVoyageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PlanningVoyage::class);
    }

    public function findByDateInterval($dtBegin, $dtEnd)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.dateBegin BETWEEN :valBegin AND :valEnd')
            ->andWhere('p.dateEnd BETWEEN :valBegin AND :valEnd')
            ->setParameter('valBegin', $dtBegin)
            ->setParameter('valEnd', $dtEnd)
            ->orderBy('p.dateBegin', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByPrix($prxMin, $prxMax)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.priceAdult BETWEEN :valMin AND :valMax')
            ->andWhere('p.priceChild BETWEEN :valMin AND :valMax')
            ->setParameter('valMin', $prxMin)
            ->setParameter('valMax', $prxMax)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function findByDateIntervalPlanning($dtBegin,$dtEnd,$visible)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.dateBegin >= :valBegin')
            ->andWhere('p.dateEnd <= :valEnd')
            ->andWhere('p.visible = :valVisible')
            ->andWhere('p.promo is null')
            ->setParameter('valVisible' , $visible)
            ->setParameter('valBegin' , $dtBegin)
            ->setParameter('valEnd' , $dtEnd)
            ->orderBy('p.dateBegin' , 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllPlanningUsingInclusion(Inclusion $inclusion)
    {
        return $this->createQueryBuilder('p')
            ->join('p.inclusion', 'i')
            ->where('i = :inclusion')
            ->setParameter('inclusion', $inclusion)
            ->getQuery()
            ->getResult()
            ;
    }
    // /**
    //  * @return PlanningVoyage[] Returns an array of PlanningVoyage objects
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
    public function findOneBySomeField($value): ?PlanningVoyage
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
