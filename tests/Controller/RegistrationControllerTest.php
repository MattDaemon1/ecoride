<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegistrationPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Créer un compte');
    }

    public function testUserCanRegisterSuccessfully(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton("S'inscrire")->form([
            'registration_form[pseudo]' => 'TestUser',
            'registration_form[email]' => 'testuser@example.com',
            'registration_form[plainPassword][first]' => 'Password123!',
            'registration_form[plainPassword][second]' => 'Password123!',
        ]);

        $client->submit($form);

        // Vérifie si la redirection vers la page de connexion est effectuée
        $this->assertResponseRedirects('/login');

        // Suivre la redirection
        $client->followRedirect();
        $this->assertSelectorTextContains('div.alert-success', 'Votre compte a été créé avec succès !');
    }
}
