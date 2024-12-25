<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchControllerTest extends WebTestCase
{
    public function testIndexReturnsResponse()
    {
        // Create a client to simulate an HTTP request
        $client = static::createClient();

        // Perform a GET request to the /search route with parameters
        $crawler = $client->request('GET', '/search', [
            'lieuDepart' => 'Paris',
            'lieuArrivee' => 'Lyon',
            'dateDepart' => '2023-10-01',
        ]);

        // Check that the response is successful (status code 200)
        $this->assertResponseIsSuccessful();

        // Check that the response content contains the expected values
        $this->assertSelectorTextContains('h1', 'Search Results'); // Ensure your template has an h1 with this text
        $this->assertSelectorTextContains('.departure', 'Paris');
        $this->assertSelectorTextContains('.arrival', 'Lyon');
        $this->assertSelectorTextContains('.date', '2023-10-01');
    }
}