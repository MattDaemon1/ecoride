<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Role;
use App\Entity\Avis;
use App\Entity\Voiture;
use App\Entity\Covoiturage;
use App\Entity\Configuration;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $user = new User();

        // Propriétés simples
        $user->setEmail('test@example.com');
        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('test@example.com', $user->getUserIdentifier()); // Test de UserInterface

        $user->setNom('Doe');
        $this->assertEquals('Doe', $user->getNom());

        $user->setPrenom('John');
        $this->assertEquals('John', $user->getPrenom());

        $user->setPseudo('johndoe');
        $this->assertEquals('johndoe', $user->getPseudo());
        $this->assertEquals('johndoe', (string) $user); // Test de __toString()

        $user->setTelephone('123456789');
        $this->assertEquals('123456789', $user->getTelephone());

        $user->setAdresse('123 Main St');
        $this->assertEquals('123 Main St', $user->getAdresse());

        $date = new \DateTime('1990-01-01');
        $user->setDateNaissance($date);
        $this->assertEquals($date, $user->getDateNaissance());

        $user->setPassword('hashed_password');
        $this->assertEquals('hashed_password', $user->getPassword());

        $roles = ['ROLE_ADMIN', 'ROLE_USER'];
        $user->setRoles($roles);
        $this->assertEquals(array_unique($roles), $user->getRoles());
    }

    public function testRelations()
    {
        $user = new User();

        // Test de la relation avec Role
        $role = new Role();
        $user->setRole($role);
        $this->assertSame($role, $user->getRole());

        // Test de la relation avec Avis
        $avis = new Avis();
        $user->addAvi($avis);
        $this->assertCount(1, $user->getAvis());
        $this->assertTrue($user->getAvis()->contains($avis));
        $this->assertSame($user, $avis->getUser());

        $user->removeAvi($avis);
        $this->assertCount(0, $user->getAvis());
        $this->assertNull($avis->getUser());

        // Test de la relation avec Voiture
        $voiture = new Voiture();
        $user->addVoiture($voiture);
        $this->assertCount(1, $user->getVoitures());
        $this->assertTrue($user->getVoitures()->contains($voiture));
        $this->assertSame($user, $voiture->getUser());

        $user->removeVoiture($voiture);
        $this->assertCount(0, $user->getVoitures());
        $this->assertNull($voiture->getUser());

        // Test de la relation avec Covoiturage
        $covoiturage = new Covoiturage();
        $user->addCovoiturage($covoiturage);
        $this->assertCount(1, $user->getCovoiturages());
        $this->assertTrue($user->getCovoiturages()->contains($covoiturage));
        $this->assertSame($user, $covoiturage->getUser());

        $user->removeCovoiturage($covoiturage);
        $this->assertCount(0, $user->getCovoiturages());
        $this->assertNull($covoiturage->getUser());

        // Test de la relation avec Configuration
        $configuration = new Configuration();
        $user->addConfiguration($configuration);
        $this->assertCount(1, $user->getConfigurations());
        $this->assertTrue($user->getConfigurations()->contains($configuration));
        $this->assertSame($user, $configuration->getUser());

        $user->removeConfiguration($configuration);
        $this->assertCount(0, $user->getConfigurations());
        $this->assertNull($configuration->getUser());
    }
}
