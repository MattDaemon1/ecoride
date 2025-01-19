<?php

namespace App\Tests\Controller;

use App\Entity\Covoiturage;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CovoiturageControllerTest extends WebTestCase
{
    public function testParticiperAsGuestRedirectsToLogin(): void
    {
        $client = static::createClient();

        // Tester un utilisateur non connecté
        $client->request('GET', '/covoiturage/1/participer');

        // Vérifier la redirection vers la page de login
        $this->assertResponseRedirects('/login');
    }

    public function testParticiperWithInvalidCovoiturageThrowsNotFoundException(): void
    {
        $client = static::createClient();

        // Simuler un utilisateur connecté
        $client->loginUser($this->createMockUser());

        // Tester un covoiturage qui n'existe pas
        $client->request('GET', '/covoiturage/999/participer');

        // Vérifier que l'exception 404 est levée
        $this->assertResponseStatusCodeSame(404);
    }

    public function testParticiperWithNoPlacesAvailable(): void
    {
        $client = static::createClient();

        // Simuler un utilisateur connecté
        $client->loginUser($this->createMockUser());

        // Simuler un covoiturage sans places
        $covoiturage = $this->createMockCovoiturage(0, 10);
        $this->mockEntityManager($client, $covoiturage);

        $client->request('GET', '/covoiturage/1/participer');

        // Vérifier que le message d'erreur est affiché
        $this->assertSelectorTextContains('.alert-danger', 'Plus de places disponibles pour ce trajet.');
    }

    public function testParticiperWithInsufficientCredits(): void
    {
        $client = static::createClient();

        // Simuler un utilisateur connecté avec des crédits insuffisants
        $user = $this->createMockUser(5);
        $client->loginUser($user);

        // Simuler un covoiturage avec un prix plus élevé
        $covoiturage = $this->createMockCovoiturage(5, 10);
        $this->mockEntityManager($client, $covoiturage);

        $client->request('GET', '/covoiturage/1/participer');

        // Vérifier que le message d'erreur est affiché
        $this->assertSelectorTextContains('.alert-danger', 'Crédits insuffisants.');
    }

    public function testSuccessfulParticipation(): void
    {
        $client = static::createClient();

        // Simuler un utilisateur connecté avec des crédits suffisants
        $user = $this->createMockUser(20);
        $client->loginUser($user);

        // Simuler un covoiturage avec des places disponibles
        $covoiturage = $this->createMockCovoiturage(5, 10);
        $this->mockEntityManager($client, $covoiturage);

        $client->request('POST', '/covoiturage/1/participer');

        // Vérifier la redirection vers la page de détails
        $this->assertResponseRedirects('/covoiturage/1');

        // Vérifier que le message de succès est affiché
        $client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'Votre participation a été confirmée.');
    }

    private function createMockUser(int $credits = 0): User
{
    $user = $this->createMock(User::class);
    $user->method('getId')->willReturn(1); // Simulez l'ID
    $user->method('getCredit')->willReturn($credits);
    $user->method('setCredit')->willReturnSelf();

    return $user;
}

    private function createMockCovoiturage(int $nbPlace, int $prixPersonne): Covoiturage
    {
        $covoiturage = new Covoiturage();
        $covoiturage->setId(1);
        $covoiturage->setNbPlace($nbPlace);
        $covoiturage->setPrixPersonne($prixPersonne);

        return $covoiturage;
    }

    private function mockEntityManager($client, $covoiturage): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $entityManager->method('getRepository')
            ->willReturnCallback(function ($class) use ($covoiturage) {
                $repository = $this->createMock(EntityRepository::class);

                if ($class === Covoiturage::class) {
                    $repository->method('find')->willReturn($covoiturage);
                }

                return $repository;
            });

        $client->getContainer()->set('doctrine.orm.entity_manager', $entityManager);
    }
}
