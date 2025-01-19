<?php

namespace App\Controller;

use App\Entity\Configuration;
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
}
