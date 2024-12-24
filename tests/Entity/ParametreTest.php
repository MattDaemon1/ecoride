<?php

namespace App\Tests\Entity;

use App\Entity\Configuration;
use App\Entity\Parametre;
use PHPUnit\Framework\TestCase;

class ParametreTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $parametre = new Parametre();

        // Test des propriétés simples
        $parametre->setPropriete('theme');
        $this->assertEquals('theme', $parametre->getPropriete());

        $parametre->setValeur('dark');
        $this->assertEquals('dark', $parametre->getValeur());

        // Relation avec Configuration
        $configuration = new Configuration(); // Simulez une configuration
        $parametre->setConfiguration($configuration);
        $this->assertSame($configuration, $parametre->getConfiguration());
    }
}
