<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findLatest(int $limit = 5): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC') // suppose que vous avez un champ 'createdAt'
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

   /**
    * @return Comment[] Returns an array of Comment objects
    */
   public function findByTrue(): array
   {
       return $this->createQueryBuilder('c')
           ->andWhere('c.isValid = :val')
           ->setMaxResults(9)
           ->setParameter('val', true)
           ->getQuery()
           ->getResult()
       ;
   }

   public function findOneByEmail(string $email)
   {
       return $this->createQueryBuilder('c')
           ->innerJoin('c.user_id', 'u')
           ->andWhere('u.email = :email')
           ->setParameter('email', $email)
           ->getQuery()
           ->getOneOrNullResult();
   }
   
}
