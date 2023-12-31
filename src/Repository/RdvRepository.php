<?php

namespace App\Repository;

use App\Entity\Rdv;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Rdv>
 *
 * @method Rdv|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rdv|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rdv[]    findAll()
 * @method Rdv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RdvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rdv::class);
    }

    public function findOverlappingReservations(\DateTimeImmutable $slotStart, \DateTimeImmutable $slotEnd)
    {
        return $this->createQueryBuilder('r')
            ->where(':slotStart < r.heure_fin AND :slotEnd > r.heure_debut')
            ->setParameters([
                'slotStart' => $slotStart,
                'slotEnd' => $slotEnd
            ])
            ->getQuery()
            ->getResult();
    }


public function findUpcomingByUser(User $user)
{
    return $this->createQueryBuilder('r')
        ->where('r.user = :user')
        ->andWhere('r.heure_debut > :now') 
        ->setParameter('user', $user)
        ->setParameter('now', new \DateTime())
        ->orderBy('r.heure_debut', 'ASC') 
        ->getQuery()
        ->getResult();
}
// Dans votre RdvRepository.php
public function findUpcomingByStatus(int $status = 1)
{
    return $this->createQueryBuilder('r')
        ->where('r.heure_debut > :now')
        ->andWhere('r.status = :status')
        ->setParameter('now', new \DateTime())
        ->setParameter('status', $status)
        ->getQuery()
        ->getResult();
}



//    /**
//     * @return Rdv[] Returns an array of Rdv objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Rdv
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
