<?php

namespace App\Controller;

use App\Entity\Configuration;
use App\Entity\Parametre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/dashboard', name: 'user_dashboard', methods: ['GET', 'POST'])]
    public function dashboard(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Charger la configuration associée à l'utilisateur
        $configuration = $entityManager->getRepository(Configuration::class)
            ->findOneBy(['user' => $user]);

        // Déterminer les rôles via les paramètres de configuration
        $roles = [];
        if ($configuration) {
            foreach ($configuration->getParametres() as $parametre) {
                if (in_array($parametre->getPropriete(), ['chauffeur', 'passager']) && $parametre->getValeur() === 'oui') {
                    $roles[] = $parametre->getPropriete();
                }
            }
        }

        // Gestion du formulaire pour les chauffeurs
        $form = null;
        if (in_array('chauffeur', $roles)) {
            $form = $this->createFormBuilder()
                ->add('plaqueImmatriculation', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                    'label' => 'Plaque d\'immatriculation',
                    'required' => true,
                ])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                // Traitement des données du formulaire
                $this->addFlash('success', 'Formulaire soumis avec succès.');
            }
        }

        // Rendre la vue
        return $this->render('user/dashboard.html.twig', [
            'roles' => $roles,
            'form' => $form ? $form->createView() : null,
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
        $parametre->setValeur(in_array($role, $submittedRoles) ? 'oui' : 'non');
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
        if (in_array($parametre->getPropriete(), ['chauffeur', 'passager']) && $parametre->getValeur() === 'oui') {
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
        $submittedRoles = $form->getData()['roles'];

        // Mettre à jour les paramètres (chauffeur/passager)
        foreach (['chauffeur', 'passager'] as $role) {
            $parametre = $this->findOrCreateParametre($configuration, $role);
            $parametre->setValeur(in_array($role, $submittedRoles) ? 'oui' : 'non');
        }

        $entityManager->persist($configuration);
        $entityManager->flush();

        $this->addFlash('success', 'Vos rôles ont été mis à jour.');
        return $this->redirectToRoute('user_dashboard');
    }

    return $this->render('user/edit_roles.html.twig', [
        'form' => $form->createView(),
    ]);
}




}
