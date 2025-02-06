<?php

namespace App\Controller;

use App\Entity\Configuration;
use App\Entity\Parametre;
use App\Entity\Voiture;
use App\Entity\Marque;
use App\Entity\Covoiturage;
use App\Entity\User;
use App\Form\CovoiturageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\VoitureType;

class UserController extends AbstractController
{
    #[Route('/user/dashboard', name: 'user_dashboard', methods: ['GET', 'POST'])]
    public function dashboard(Request $request, EntityManagerInterface $entityManager, VoitureType $voitureType, CovoiturageType $covoiturageType): Response
    {
        $user = $this->getUser();

        if (!$user) {
            // Handle the case where the user is not authenticated
            return $this->redirectToRoute('app_login');
        }

        // Charger la configuration associée à l'utilisateur
        $configuration = $entityManager->getRepository(Configuration::class)
            ->findOneBy(['user' => $user]);

        // Déterminer les rôles via les paramètres de configuration
        $roles = [];
        if ($configuration) {
            foreach ($configuration->getParametres() as $parametre) {
                $roles[] = $parametre->getPropriete();
            }
        }

        // Gestion du formulaire pour les chauffeurs
    $form = null;
    if (in_array('chauffeur', $roles)) {
// Formulaire pour ajouter un véhicule
$voiture = new Voiture();
$voitureForm = $this->createForm(VoitureType::class, $voiture, [
    'csrf_protection' => true,
]);
$voitureForm->handleRequest($request);

// Formulaire pour ajouter un covoiturage
$covoiturage = new Covoiturage();
$covoiturageForm = $this->createForm(CovoiturageType::class, $covoiturage, [
    'user' => $this->getUser(),
    'csrf_protection' => true,
]);

$covoiturageForm->handleRequest($request);

// Gérer la soumission du formulaire Voiture
if ($voitureForm->isSubmitted() && $voitureForm->isValid()) {
    $voiture->setUser($this->getUser());
    try {
        $entityManager->persist($voiture);
        $entityManager->flush();
        $this->addFlash('success', 'Le véhicule a été ajouté avec succès.');
    } catch (\Exception $e) {
        $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout du véhicule.');
    }
}

// Gérer la soumission du formulaire Covoiturage
if ($covoiturageForm->isSubmitted() && $covoiturageForm->isValid()) {
    $covoiturage->setUser($this->getUser());
    $entityManager->persist($covoiturage);
    $entityManager->flush();
    $this->addFlash('success', 'Le covoiturage a été ajouté avec succès.');
}

// Passer les bons formulaires à Twig
return $this->render('user/dashboard.html.twig', [
    'roles' => $roles,
    'voitureForm' => $voitureForm->createView(), // Variable distincte
    'covoiturageForm' => $covoiturageForm->createView(), // Variable distincte
    'user' => $user,
]);
    } else {
        return $this->render('user/dashboard.html.twig', [
            'roles' => $roles,
            'user' => $user,
        ]);
    }
}

    private function findOrCreateParametre(Configuration $configuration, string $propriete): Parametre
    {
        foreach ($configuration->getParametres() as $parametre) {
            if ($parametre->getPropriete() === $propriete) {
                return $parametre;
            }
        }

        $parametre = new Parametre();
        $parametre->setConfiguration($configuration);
        $parametre->setPropriete($propriete);
        $configuration->addParametre($parametre);

        return $parametre;
}
    #[Route('/user/update-roles', name: 'user_update_roles', methods: ['POST'])]
    public function updateRoles(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
    
        // Récupérer ou créer la configuration de l'utilisateur
        $configuration = $entityManager->getRepository(Configuration::class)
            ->findOneBy(['user' => $user]) ?? new Configuration();
        $configuration->setUser($user);
    
        // Récupérer les rôles soumis depuis le formulaire
        $submittedRoles = $request->request->get('roles', []);
        if (!is_array($submittedRoles)) {
            $submittedRoles = [];
        }
    
        // Mettre à jour les paramètres (chauffeur/passager)
        foreach (['chauffeur', 'passager'] as $role) {
            $parametre = $this->findOrCreateParametre($configuration, $role);
            $parametre->setValeur(in_array($role, $submittedRoles));
        }
    
        // Sauvegarder la configuration mise à jour
        $entityManager->persist($configuration);
        $entityManager->flush();
    
        $this->addFlash('success', 'Vos rôles ont été mis à jour.');
        return $this->redirectToRoute('user_dashboard');
    }
}