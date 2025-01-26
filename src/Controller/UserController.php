<?php

namespace App\Controller;

use App\Entity\Configuration;
use App\Entity\Parametre;
use App\Entity\Voiture;
use App\Entity\Marque;
use App\Entity\Covoiturage;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\VoitureType;

class UserController extends AbstractController
{
    #[Route('/user/dashboard', name: 'user_dashboard', methods: ['GET', 'POST'])]
    public function dashboard(Request $request, EntityManagerInterface $entityManager, VoitureType $voitureType): Response
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
        $voiture = new Voiture();
        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $voiture->setUser($user);
            $entityManager->persist($voiture);
            $entityManager->flush();

            $this->addFlash('success', 'Le véhicule a été ajouté avec succès.');
            return $this->redirectToRoute('user_dashboard');
        }
    }

    return $this->render('user/dashboard.html.twig', [
        'roles' => $roles,
        'voitureType' => $form ? $form->createView() : null,
        'user' => $user,
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

    #[Route('/user/update-roles', name: 'user_update_roles', methods: ['POST'])]
    public function updateRoles(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Récupérer ou créer la configuration de l'utilisateur
        $configuration = $entityManager->getRepository(Configuration::class)
            ->findOneBy(['user' => $user]) ?? new Configuration();
        $configuration->setUser($user);

        // Récupérer les rôles soumis depuis le formulaire
        $submittedData = $request->request->all(); // Récupère toutes les données POST
        $submittedRoles = $submittedData['roles'] ?? []; // Vérifie si 'roles' est défini, sinon tableau vide

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

    #[Route('/user/edit-roles', name: 'user_edit_roles', methods: ['GET', 'POST'])]
    public function editRoles(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Charger ou créer la configuration de l'utilisateur
        $configuration = $entityManager->getRepository(Configuration::class)
            ->findOneBy(['user' => $user]) ?? new Configuration();
        $configuration->setUser($user);

        // Récupérer les rôles actuels
        $roles = [];
        foreach ($configuration->getParametres() as $parametre) {
            if ($parametre->getValeur()) {
                $roles[] = $parametre->getPropriete();
            }
        }

        // Formulaire pour modifier les rôles
        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('roles', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
            'choices' => [
                'Chauffeur' => 'chauffeur',
                'Passager' => 'passager',
            ],
            'expanded' => true,
            'multiple' => true,
            'data' => $roles,
        ]);
        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle form submission
        }

        return $this->render('user/edit_roles.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}