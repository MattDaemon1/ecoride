<?php

namespace App\Controller;

use App\Entity\Configuration;
use App\Entity\Parametre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ConfigurationController extends AbstractController
{
    #[Route('/user/configuration', name: 'user_configuration', methods: ['GET', 'POST'])]
public function manageConfiguration(Request $request, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();

    // Vérifier si une configuration existe déjà
    $configuration = $entityManager->getRepository(Configuration::class)->findOneBy(['user' => $user]) ?? new Configuration();
    $configuration->setUser($user);

    if ($request->isMethod('POST')) {
        // Récupérer les données du formulaire
        $role = $request->request->get('role');
        $preferences = $request->request->get('preferences') ?? [];
        $customPreferences = $request->request->get('custom_preferences') ?? '';

        // Mettre à jour le paramètre "role"
        $roleParam = $this->findOrCreateParametre($configuration, 'role');
        $roleParam->setValeur($role);

        // Mettre à jour les autres préférences
        foreach (['fumeur', 'animaux'] as $preference) {
            $param = $this->findOrCreateParametre($configuration, $preference);
            $param->setValeur(in_array($preference, $preferences) ? 'oui' : 'non');
        }

        // Préférences personnalisées
        $customParam = $this->findOrCreateParametre($configuration, 'custom_preferences');
        $customParam->setValeur($customPreferences);

        $entityManager->persist($configuration);
        $entityManager->flush();

        $this->addFlash('success', 'Votre configuration a été mise à jour avec succès.');
        return $this->redirectToRoute('user_dashboard');
    }

    return $this->render('user/configuration.html.twig', [
        'configuration' => $configuration,
    ]);
}

private function findOrCreateParametre(Configuration $configuration, string $propriete): Parametre
{
    foreach ($configuration->getParametres() as $parametre) {
        if ($parametre->getPropriete() === $propriete) {
            return $parametre;
        }
    }

    $parametre = new Parametre();
    $parametre->setPropriete($propriete);
    $parametre->setConfiguration($configuration);
    $configuration->addParametre($parametre);

    return $parametre;
}

    
    #[Route('/configuration', name: 'app_configuration')]
    public function index(): Response
    {
        return $this->render('configuration/index.html.twig', [
            'controller_name' => 'ConfigurationController',
        ]);
    }
}
