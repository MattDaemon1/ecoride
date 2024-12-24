<?php

namespace App\Tests\Entity;

use App\Entity\Marque;
use App\Entity\Voiture;
use PHPUnit\Framework\TestCase;

class MarqueTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $marque = new Marque();

        // Test de l'ID (par défaut null)
        $this->assertNull($marque->getId());

        // Test du libellé
        $marque->setLibelle('Toyota');
        $this->assertEquals('Toyota', $marque->getLibelle());

        // Test de __toString()
        $this->assertEquals('Toyota', (string) $marque);
    }

    public function testAddAndRemoveVoiture()
    {
        $marque = new Marque();
        $voiture = new Voiture();

        // Test d'ajout d'une voiture
        $marque->addVoiture($voiture);
        $this->assertCount(1, $marque->getVoiture());
        $this->assertTrue($marque->getVoiture()->contains($voiture));
        $this->assertSame($marque, $voiture->getMarque()); // Vérifie que la relation inverse est mise à jour

        // Test de suppression d'une voiture
        $marque->removeVoiture($voiture);
        $this->assertCount(0, $marque->getVoiture());
        $this->assertFalse($marque->getVoiture()->contains($voiture));
        $this->assertNull($voiture->getMarque()); // Vérifie que la relation inverse est annulée
    }
}
