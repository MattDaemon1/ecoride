<?php

namespace App\Controller;

use App\Entity\Covoiturage;
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
        if ($covoiturage->getNombrePlaces() <= 0) {
            $this->addFlash('error', 'Plus de places disponibles pour ce trajet.');
            return $this->redirectToRoute('covoiturage_detail', ['id' => $id]);
        }

        if ($user->getCredits() < $covoiturage->getPrix()) {
            $this->addFlash('error', 'Crédits insuffisants.');
            return $this->redirectToRoute('covoiturage_detail', ['id' => $id]);
        }

        // Double confirmation
        if ($request->isMethod('POST')) {
            // Mise à jour des données
            $user->setCredits($user->getCredits() - $covoiturage->getPrix());
            $covoiturage->setNombrePlaces($covoiturage->getNombrePlaces() - 1);

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
}


