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
}