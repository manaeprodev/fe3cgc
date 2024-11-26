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

    public function lockFaction(int $factionId, int $userId)
    {
        $query = $this->getEntityManager()->createQuery(
                    'UPDATE App\Entity\User u
                    SET u.firstCo = 0, u.faction = :factionId
                    WHERE u.id = :userId'
                );

        $query->setParameter('factionId', $factionId)
            ->setParameter('userId', $userId);

        $query->execute();

    }

    public function getUnitsByTerritoryId(int $territoryId)
    {
        return $this->createQueryBuilder('u')
            ->join('u.territory', 't')
            ->join('u.faction', 'f')
            ->join('u.title', 'ti')
            ->select('u.id, u.username, u.avatar, u.renown, f.id AS factionId, ti.id AS titleId, ti.name AS title, ti.level, ti.miniLabel, t.id AS territoryId')
            ->andWhere('t.id = :territoryId')
            ->setParameter('territoryId', $territoryId)
            ->getQuery()
            ->getResult();
    }

    public function positionUnit(int $userId, int $territoryId)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'UPDATE App\Entity\User u
             SET u.territory = :newTerritoryId
             WHERE u.id = :userId'
        );

        $query->setParameter('newTerritoryId', $territoryId);
        $query->setParameter('userId', $userId);

        $result = $query->execute();
    }

    public function getLeaderboard()
    {
        return $this->createQueryBuilder('u')
            ->join('u.faction', 'f')
            ->join('u.title', 'ti')
            ->select('u.username, u.avatar, u.renown, f.id as factionId, f.name as faction, ti.id as titleId, ti.name as title, ti.label')
            ->andWhere('u.id not in (10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46)')
            ->andWhere('u.firstCo = 0')
            ->orderBy('u.renown desc')
            ;
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
