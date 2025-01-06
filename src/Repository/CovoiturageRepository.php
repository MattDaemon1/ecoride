<?php


// src/Repository/CovoiturageRepository.php
// src/Repository/CovoiturageRepository.php
namespace App\Repository;

use App\Entity\Covoiturage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CovoiturageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Covoiturage::class);
    }

    public function findByCriteria($lieuDepart, $lieuArrivee, $dateDepart)
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.user', 'u')
            ->join('c.voiture', 'v')
            ->join('v.marque', 'm')
            ->select('c', 'u', 'v', 'm')
            ->where('c.lieuDepart = :lieuDepart')
            ->andWhere('c.lieuArrivee = :lieuArrivee')
            ->andWhere('c.dateDepart = :dateDepart')
            ->andWhere('c.nbPlace > 0')
            ->orderBy('c.heureDepart', 'ASC')
            ->setParameter('lieuDepart', $lieuDepart)
            ->setParameter('lieuArrivee', $lieuArrivee)
            ->setParameter('dateDepart', $dateDepart);

        return $qb->getQuery()->getResult();
    }

    public function findUpcomingResults($lieuDepart, $lieuArrivee)
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.user', 'u')
            ->join('c.voiture', 'v')
            ->join('v.marque', 'm')
            ->select('c', 'u', 'v', 'm')
            ->where('c.lieuDepart = :lieuDepart')
            ->andWhere('c.lieuArrivee = :lieuArrivee')
            ->andWhere('c.dateDepart > :today')
            ->andWhere('c.nbPlace > 0')
            ->setParameter('lieuDepart', $lieuDepart)
            ->setParameter('lieuArrivee', $lieuArrivee)
            ->setParameter('today', new \DateTime())
            ->orderBy('c.dateDepart', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findNextAvailable(string $lieuDepart, string $lieuArrivee, \DateTime $date): ?Covoiturage
{
    return $this->createQueryBuilder('c')
        ->where('c.lieuDepart = :depart')
        ->andWhere('c.lieuArrivee = :arrivee')
        ->andWhere('c.dateDepart > :date')
        ->andWhere('c.nbPlace > 0')
        ->setParameter('depart', $lieuDepart)
        ->setParameter('arrivee', $lieuArrivee)
        ->setParameter('date', $date)
        ->orderBy('c.dateDepart', 'ASC')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
}

public function findFilteredCovoiturages(array $filters): array
{
    $qb = $this->createQueryBuilder('c');

    // Recherche de base : lieu de départ, lieu d'arrivée, date de départ
    if (!empty($filters['lieuDepart'])) {
        $qb->andWhere('c.lieuDepart = :lieuDepart')
           ->setParameter('lieuDepart', $filters['lieuDepart']);
    }

    if (!empty($filters['lieuArrivee'])) {
        $qb->andWhere('c.lieuArrivee = :lieuArrivee')
           ->setParameter('lieuArrivee', $filters['lieuArrivee']);
    }

    if (!empty($filters['dateDepart'])) {
        $qb->andWhere('c.dateDepart = :dateDepart')
           ->setParameter('dateDepart', $filters['dateDepart']);
    }

    // Récupérer les résultats initiaux basés sur la recherche de base
    $results = $qb->getQuery()->getResult();

    // Appliquer les filtres supplémentaires
    $results = array_filter($results, function ($covoiturage) use ($filters) {
        // Filtrer par prix maximum
        if (!empty($filters['prixMax']) && $covoiturage->getPrixPersonne() > $filters['prixMax']) {
            return false;
        }

        // Filtrer par durée maximale
        if (!empty($filters['dureeMax'])) {
            $dateDepart = $covoiturage->getDateDepart();
            $heureDepart = $covoiturage->getHeureDepart();
            $dateArrivee = $covoiturage->getDateArrivee();
            $heureArrivee = $covoiturage->getHeureArrivee();

            // Combiner date et heure pour créer des DateTime complètes
            $dateTimeDepart = (clone $dateDepart)->setTime(
                $heureDepart->format('H'),
                $heureDepart->format('i')
            );
            $dateTimeArrivee = (clone $dateArrivee)->setTime(
                $heureArrivee->format('H'),
                $heureArrivee->format('i')
            );

            // Calculer la durée en minutes
            $diffMinutes = ($dateTimeArrivee->getTimestamp() - $dateTimeDepart->getTimestamp()) / 60;

            if ($diffMinutes > $filters['dureeMax']) {
                return false;
            }
        }

        // Filtrer par voyage écologique
        if (!empty($filters['ecologique']) && $covoiturage->getVoiture()->getEnergie() !== 'Électrique') {
            return false;
        }

        // Filtrer par note minimale
        if (!empty($filters['noteMinimale'])) {
            $notes = array_map(function ($avis) {
                return $avis->getNote();
            }, $covoiturage->getUser()->getAvis()->toArray());

            $averageNote = !empty($notes) ? array_sum($notes) / count($notes) : 0;

            if ($averageNote < $filters['noteMinimale']) {
                return false;
            }
        }

        return true; // Conserve les covoiturages qui passent tous les filtres
    });

    return $results;
}

    

}