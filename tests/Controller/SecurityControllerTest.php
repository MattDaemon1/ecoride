<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageIsAccessible(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Identifiez-vous');
    }

    public function testLoginWithValidCredentials(): void
    {
        $client = static::createClient();

        // Crée un utilisateur dans la base de données de test
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $user = new \App\Entity\User();
        $user->setEmail('user@example.com');
        $user->setPassword(
            $client->getContainer()->get('security.password_hasher')
                ->hashPassword($user, 'password')
        );
        $entityManager->persist($user);
        $entityManager->flush();

        // Teste la connexion
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'user@example.com',
            '_password' => 'password',
        ]);
        $client->submit($form);

        // Vérifie si l'utilisateur est redirigé après connexion
        $this->assertResponseRedirects('/');
    }
}
