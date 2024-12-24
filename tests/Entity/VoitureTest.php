<?php

namespace App\Tests\Entity;

use App\Entity\Voiture;
use App\Entity\User;
use App\Entity\Marque;
use App\Entity\Covoiturage;
use PHPUnit\Framework\TestCase;

class VoitureTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $voiture = new Voiture();

        // Test des propriétés simples
        $voiture->setModele('Model S');
        $this->assertEquals('Model S', $voiture->getModele());

        $voiture->setImmatriculation('AB-123-CD');
        $this->assertEquals('AB-123-CD', $voiture->getImmatriculation());

        $voiture->setEnergie('Électrique');
        $this->assertEquals('Électrique', $voiture->getEnergie());

        $voiture->setCouleur('Rouge');
        $this->assertEquals('Rouge', $voiture->getCouleur());

        $date = new \DateTime('2020-01-01');
        $voiture->setDatePremiereImmatriculation($date);
        $this->assertEquals($date, $voiture->getDatePremiereImmatriculation());
    }

    public function testRelations()
    {
        $voiture = new Voiture();

        // Relation avec User
        $user = new User();
        $voiture->setUser($user);
        $this->assertSame($user, $voiture->getUser());

        // Relation avec Marque
        $marque = new Marque();
        $marque->setLibelle('Tesla');
        $voiture->setMarque($marque);
        $voiture->setModele('Model S'); // Assurez-vous que le modèle est défini avant de tester
        $this->assertEquals('Tesla Model S', (string) $voiture);


        // Relation avec Covoiturage
        $covoiturage = new Covoiturage();
        $voiture->addCovoiturage($covoiturage);
        $this->assertCount(1, $voiture->getCovoiturages());
        $this->assertTrue($voiture->getCovoiturages()->contains($covoiturage));
        $this->assertSame($voiture, $covoiturage->getVoiture());

        $voiture->removeCovoiturage($covoiturage);
        $this->assertCount(0, $voiture->getCovoiturages());
        $this->assertNull($covoiturage->getVoiture());
    }
}
