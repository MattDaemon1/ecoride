<?php

namespace App\Tests\Entity;

use App\Entity\Avis;
use App\Entity\Covoiturage;
use App\Entity\User;
use App\Entity\Voiture;
use PHPUnit\Framework\TestCase;

class CovoiturageTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $covoiturage = new Covoiturage();

        // Propriétés simples
        $covoiturage->setLieuDepart('Paris');
        $this->assertEquals('Paris', $covoiturage->getLieuDepart());

        $covoiturage->setLieuArrivee('Lyon');
        $this->assertEquals('Lyon', $covoiturage->getLieuArrivee());

        $covoiturage->setStatut('actif');
        $this->assertEquals('actif', $covoiturage->getStatut());

        $covoiturage->setNbPlace(4);
        $this->assertEquals(4, $covoiturage->getNbPlace());

        $covoiturage->setPrixPersonne(20.50);
        $this->assertEquals(20.50, $covoiturage->getPrixPersonne());

        // Relation avec User
        $user = new User();
        $covoiturage->setUser($user);
        $this->assertSame($user, $covoiturage->getUser());

        // Relation avec Voiture
        $voiture = new Voiture();
        $covoiturage->setVoiture($voiture);
        $this->assertSame($voiture, $covoiturage->getVoiture());

        // Relation avec Avis
        $avis = new Avis();
        $covoiturage->addAvi($avis);
        $this->assertCount(1, $covoiturage->getAvis());
        $this->assertTrue($covoiturage->getAvis()->contains($avis));

        $covoiturage->removeAvi($avis);
        $this->assertCount(0, $covoiturage->getAvis());
    }
}
