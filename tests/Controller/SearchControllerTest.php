<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchControllerTest extends WebTestCase
{
    public function testSearchWithResults(): void
    {
        $client = static::createClient();

        // Simule une requête GET avec des critères valides
        $client->request('GET', '/search', [
            'lieuDepart' => 'Paris',
            'lieuArrivee' => 'Nice',
            'dateDepart' => '2025-01-01'
        ]);

        // Vérifie que la page s'affiche correctement
        $this->assertResponseIsSuccessful();

        // Vérifie que les résultats s'affichent
        $this->assertSelectorExists('.card');
        $this->assertSelectorTextContains('.card-title', 'JaneSmith');
    }

    public function testSearchWithNoResults(): void
    {
        $client = static::createClient();

        // Simule une requête GET avec des critères ne correspondant à aucun covoiturage
        $client->request('GET', '/search', [
            'lieuDepart' => 'NonExistentCity',
            'lieuArrivee' => 'AnotherNonExistentCity',
            'dateDepart' => '2025-01-01'
        ]);

        // Vérifie que la page s'affiche correctement
        $this->assertResponseIsSuccessful();

        // Vérifie qu'un message "Aucun résultat" s'affiche
        $this->assertSelectorTextContains('.alert-warning', 'Aucun covoiturage trouvé');
    }
}
