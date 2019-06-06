<?php

namespace App\Repository;

use App\Entity\VoyageOrganise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method VoyageOrganise|null find($id, $lockMode = null, $lockVersion = null)
 * @method VoyageOrganise|null findOneBy(array $criteria, array $orderBy = null)
 * @method VoyageOrganise[]    findAll()
 * @method VoyageOrganise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoyageOrganiseRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, VoyageOrganise::class);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function findByVisible($value)
    {
        return $this->createQueryBuilder('vo')
            ->andWhere('vo.visible = :visibilite')
            ->setParameter('visibilite', $value)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteArchive($value1)
    {
        return $this->createQueryBuilder('vo')
            ->andWhere('vo.id = :id')
           // ->andWhere('vo.visible = :visibilite')
            ->setParameter('id', $value1)
            ->getQuery()
            ->getResult()
            ;
    }



    // /**
    //  * @return VoyageOrganise[] Returns an array of VoyageOrganise objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VoyageOrganise
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
