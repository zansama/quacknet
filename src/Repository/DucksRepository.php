<?php

namespace App\Repository;

use App\Entity\Ducks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Ducks|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ducks|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ducks[]    findAll()
 * @method Ducks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DucksRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Ducks::class);
    }

   /* public function loadUserByUsername($usernameOrEmail)
    {
        dump($usernameOrEmail);
        die();
        return $this->createQueryBuilder('u')
            ->where('u.duckname = :query OR u.email = :query')
            ->setParameter('query', $usernameOrEmail)
            ->getQuery()
            ->getOneOrNullResult();
    }*/
    // /**
    //  * @return Ducks[] Returns an array of Ducks objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ducks
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /**
     * Loads the user for the given username.
     *
     * This method must return null if the user is not found.
     *
     * @param string $username The username
     *
     * @return UserInterface|null
     */
    public function loadUserByUsername($username)
    {

        return $this->createQueryBuilder('u')
            ->where('u.duckname = :query OR u.email = :query')
            ->setParameter('query', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
