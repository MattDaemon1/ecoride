<?php

namespace App\Controller;

use App\Entity\Covoiturage;
use App\Entity\Voiture;
use App\Form\CovoiturageType;
use App\Entity\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VoyageController extends AbstractController
{
    #[Route('/voyage/new', name: 'saisir_un_voyage', methods: ['GET', 'POST'])]
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

        return $this->render('voyage/voyage.html.twig', [
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
}
