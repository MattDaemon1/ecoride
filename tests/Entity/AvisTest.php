<?php

namespace App\Tests\Entity;

use App\Entity\Avis;
use App\Entity\Covoiturage;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class AvisTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $avis = new Avis();

        // Test des propriétés simples
        $avis->setCommentaire('Très bon covoiturage');
        $this->assertEquals('Très bon covoiturage', $avis->getCommentaire());

        $avis->setNote('5 étoiles');
        $this->assertEquals('5 étoiles', $avis->getNote());

        $avis->setStatut('validé');
        $this->assertEquals('validé', $avis->getStatut());

        // Test des relations avec User
        $user = new User(); // Simulez un utilisateur ou utilisez un Mock si nécessaire
        $avis->setUser($user);
        $this->assertSame($user, $avis->getUser());

        // Test des relations avec Covoiturage
        $covoiturage = new Covoiturage(); // Simulez un covoiturage ou utilisez un Mock si nécessaire
        $avis->setCovoiturage($covoiturage);
        $this->assertSame($covoiturage, $avis->getCovoiturage());
    }
}
