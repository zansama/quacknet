<?php

namespace App\Repository;

use App\Entity\Quack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Quack|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quack|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quack[]    findAll()
 * @method Quack[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuackRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Quack::class);
    }



     /**
      * @return Quack[] Returns an array of Quack objects
      */

    public function findByDuckname($duckname)
    {
        return $this->createQueryBuilder('q')
            ->join('q.author', 'c')
            ->addSelect('c')
            ->orWhere('c.duckname LIKE :val')
            ->orWhere('q.content LIKE :val')
            ->setParameter('val', '%'.$duckname.'%')
            ->orderBy('q.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findBytags($tags)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.tags = :val')
            ->setParameter('val', $tags)
            ->orderBy('q.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }




    /*
    public function findOneBySomeField($value): ?Quack
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
