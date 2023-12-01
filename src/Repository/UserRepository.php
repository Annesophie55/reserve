<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
* @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function searchUsers($query)
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.name LIKE :query')
            ->orWhere('u.firstName LIKE :query')
            ->orWhere('u.email LIKE :query')
            ->setParameter('query', '%'.$query.'%');
    
        return $qb->getQuery()->getResult();
    }
    
    
/**
* Renvoie tous les utilisateurs sauf ceux ayant le rôle admin
* @return User[]
*/
public function findAllExceptAdmin(): array
{
    return $this->createQueryBuilder('u')
        ->andWhere('u.roles NOT LIKE :role') 
        ->setParameter('role', '%"ROLE_ADMIN"%') 
        ->orderBy('u.id', 'ASC')
        ->getQuery()
        ->getResult()
    ;
}

   public function findOneByEmail($email): ?User
   {
       return $this->createQueryBuilder('u')
           ->andWhere('u.email = :val')
           ->setParameter('val', $email)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }
}
