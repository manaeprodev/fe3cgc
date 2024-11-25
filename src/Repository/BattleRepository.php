<?php

namespace App\Repository;

use App\Entity\Battle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Battle>
 */
class BattleRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Battle::class);
        $this->entityManager = $entityManager;
    }

    public function getExistingBattle(int $territoryId): ?Battle
    {
        return $this->createQueryBuilder('b')
            ->join('b.territoryId', 't')
            ->andWhere('b.status = :status')
            ->andWhere('t.id = :territoryId')
            ->setParameter('status', 1)
            ->setParameter('territoryId', $territoryId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findBattlesInProgress(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.status = :status')  // On filtre par le statut
            ->andWhere('b.endDateTime < :currentDateTime')  // Vérifie que endDateTime est dans le futur
            ->setParameter('status', 1)  // Le statut 1 signifie "en cours"
            ->setParameter('currentDateTime', new \DateTime())  // On passe la DateTime actuelle
            ->getQuery()
            ->getResult();  // Exécute la requête et récupère les résultats
    }

    public function updateBattlesStatusToFinished(): array
    {
        // Récupère toutes les batailles dont le statut est encore "en cours" et dont la date de fin est passée
        $battlesInProgress = $this->findBattlesInProgress();

        // Si des batailles sont retournées
        if (count($battlesInProgress) > 0) {
            foreach ($battlesInProgress as $battle) {
                // Met à jour le statut de chaque bataille
                $battle->setStatus(2);  // 2 signifie "terminé"
            }

            // Persiste les changements en BDD
            $this->entityManager->flush();
        }

        return $battlesInProgress;
    }

    //    /**
    //     * @return Battle[] Returns an array of Battle objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Battle
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
