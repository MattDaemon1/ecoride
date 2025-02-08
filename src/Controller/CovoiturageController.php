<?php

namespace App\Controller;

use App\Entity\Configuration;
use App\Entity\Covoiturage;
use App\Entity\Voiture;
use App\Form\CovoiturageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CovoiturageController extends AbstractController
{
    #[Route('/covoiturage/{id}/participer', name: 'covoiturage_participer', methods: ['GET', 'POST'])]
    public function participer(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $covoiturage = $entityManager->getRepository(Covoiturage::class)->find($id);

        if (!$covoiturage) {
            throw $this->createNotFoundException('Covoiturage non trouvé.');
        }

        // Vérification des places et crédits
        if ($covoiturage->getNbPlace() <= 0) {
            $this->addFlash('error', 'Plus de places disponibles pour ce trajet.');
            return $this->redirectToRoute('covoiturage_detail', ['id' => $id]);
        }

        if ($user->getCredit() < $covoiturage->getPrixPersonne()) {
            $this->addFlash('error', 'Crédits insuffisants.');
            return $this->redirectToRoute('covoiturage_detail', ['id' => $id]);
        }

    // Ajoutez ici la logique pour confirmer la participation

    $this->addFlash('success', 'Votre participation a été confirmée.');
    return $this->redirectToRoute('covoiturage_detail', ['id' => $id]);

        // Double confirmation
        if ($request->isMethod('POST')) {
            // Mise à jour des données
            $user->setCredit($user->getCredit() - $covoiturage->getPrixPersonne());
            $covoiturage->setNbPlace($covoiturage->getNbPlace() - 1);

            $entityManager->persist($user);
            $entityManager->persist($covoiturage);
            $entityManager->flush();

            $this->addFlash('success', 'Votre participation a été confirmée.');
            return $this->redirectToRoute('covoiturage_detail', ['id' => $id]);
        }

        return $this->render('covoiturage/participer.html.twig', [
            'covoiturage' => $covoiturage,
            'user' => $user
        ]);
    }

    #[Route('/covoiturage', name: 'app_covoiturage')]
    public function index(): Response
    {
        return $this->render('covoiturage/index.html.twig', [
            'controller_name' => 'CovoiturageController',
        ]);
    }

    #[Route('/covoiturage/{id}', name: 'covoiturage_detail', methods: ['GET'])]
public function detail(int $id, EntityManagerInterface $entityManager): Response
{
    $covoiturage = $entityManager->getRepository(Covoiturage::class)->find($id);

    if (!$covoiturage) {
        throw $this->createNotFoundException('Covoiturage non trouvé.');
    }

    return $this->render('covoiturage/detail.html.twig', [
        'covoiturage' => $covoiturage,
    ]);
}

#[Route('/covoiturage/new', name: 'saisir_un_voyage', methods: ['GET', 'POST'])]
public function saisirVoyage(Request $request, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    // ✅ Récupérer les rôles de l'utilisateur
    $roles = $this->getUserRoles($entityManager, $user);
    

    // ✅ Récupérer les voitures de l'utilisateur connecté
    $voitures = $entityManager->getRepository(Voiture::class)->findBy(['user' => $user]);

    // ✅ Création du formulaire avec les voitures filtrées
    $covoiturage = new Covoiturage();
    $form = $this->createForm(CovoiturageType::class, $covoiturage, [
        'user' => $user,
        'csrf_protection' => true,
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $covoiturage->setUser($user);
        $entityManager->persist($covoiturage);
        $entityManager->flush();

        $this->addFlash('success', 'Le covoiturage a été ajouté avec succès.');
        return $this->redirectToRoute('user_dashboard');
    }

    return $this->render('covoiturage/voyage.html.twig', [
        'covoiturageForm' => $form->createView(),
        'roles' => $roles,
        'voitures' => $voitures, // ✅ Passage des voitures à la vue
    ]);
}

/**
 * Récupère les rôles de l'utilisateur à partir de la configuration.
 */
private function getUserRoles(EntityManagerInterface $entityManager, $user): array
{
    $configuration = $entityManager->getRepository(Configuration::class)->findOneBy(['user' => $user]);
    $roles = [];

    if ($configuration) {
        foreach ($configuration->getParametres() as $parametre) {
            if ($parametre->getValeur()) {
                $roles[] = $parametre->getPropriete();
            }
        }
    }

    return $roles;
}

#[Route('/{id}/edit', name: 'covoiturages_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
{
    $covoiturage = $entityManager->getRepository(Covoiturage::class)->find($id);

    if (!$covoiturage) {
        throw $this->createNotFoundException('Le covoiturage demandé n\'existe pas.');
    }

    $form = $this->createForm(CovoiturageType::class, $covoiturage);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        $this->addFlash('success', 'Covoiturage mis à jour avec succès.');
        return $this->redirectToRoute('covoiturages_index');
    }

    return $this->render('covoiturage/edit.html.twig', [
        'covoiturage' => $covoiturage,
        'form' => $form->createView(),
    ]);
}

#[Route('/covoiturage/delete/{id}', name: 'covoiturages_delete', methods: ['POST'])]
public function delete(Request $request, Covoiturage $covoiturage, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete' . $covoiturage->getId(), $request->request->get('_token'))) {
        $entityManager->remove($covoiturage);
        $entityManager->flush();

        $this->addFlash('success', 'Le covoiturage a été supprimé avec succès.');
    }

    return $this->redirectToRoute('user_dashboard');
}

}



