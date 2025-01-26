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

    #[Route('/{id}/delete', name: 'voiture_delete', methods: ['POST', 'DELETE'])]
public function delete(Request $request, Voiture $voiture, EntityManagerInterface $entityManager): Response
{
    // Vérifier le token CSRF pour la sécurité
    if ($this->isCsrfTokenValid('delete' . $voiture->getId(), $request->request->get('_token'))) {
        $entityManager->remove($voiture);
        $entityManager->flush();

        // Redirection après la suppression
        $this->addFlash('success', 'Le véhicule a été supprimé avec succès.');
        return $this->redirectToRoute('user_dashboard');
    }

    $this->addFlash('error', 'Action non autorisée.');
    return $this->redirectToRoute('user_dashboard');
}
}
