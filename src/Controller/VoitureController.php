<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Form\VoitureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/voiture')]
class VoitureController extends AbstractController
{
    #[Route('/{id}/edit', name: 'voiture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Voiture $voiture, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('user_dashboard', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('voiture/edit.html.twig', [
            'voiture' => $voiture,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'voiture_delete', methods: ['POST'])]
public function delete(Request $request, Voiture $voiture, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete'.$voiture->getId(), $request->request->get('_token'))) {
        $entityManager->remove($voiture);
        $entityManager->flush();
        $this->addFlash('success', 'Véhicule supprimé avec succès');
    }

    return $this->redirectToRoute('user_dashboard');
}



#[Route('/voiture/new', name: 'voiture_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();
    
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    $voiture = new Voiture();
    $form = $this->createForm(VoitureType::class, $voiture);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $voiture->setUser($user); // ✅ Lier la voiture à l'utilisateur connecté
        $entityManager->persist($voiture);
        $entityManager->flush();

        $this->addFlash('success', 'Le véhicule a été ajouté avec succès.');
        return $this->redirectToRoute('user_dashboard');
    }

    return $this->render('voiture/new.html.twig', [
        'voitureForm' => $form->createView(),
    ]);
}


    
}
