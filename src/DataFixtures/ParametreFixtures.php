<?php

namespace App\DataFixtures;

use App\Entity\Parametre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ParametreFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $parametres = [
            ['propriete' => 'chauffeur', 'valeur' => 'oui'],
            ['propriete' => 'passager', 'valeur' => 'oui'],
            ['propriete' => 'fumeur', 'valeur' => 'non'],
            ['propriete' => 'animaux', 'valeur' => 'oui'],
            ['propriete' => 'autre', 'valeur' => 'Texte libre pour une configuration personnalisée.'],
        ];

        foreach ($parametres as $data) {
            $parametre = new Parametre();
            $parametre->setPropriete($data['propriete']);
            $parametre->setValeur($data['valeur']);
            $manager->persist($parametre);
        }

        $manager->flush();
    }
}
