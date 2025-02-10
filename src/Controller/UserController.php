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
    public function dashboard(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
    
        if (!$user) {
            // Rediriger si l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_login');
        }
    
        // Charger la configuration associée à l'utilisateur
        $configuration = $entityManager->getRepository(Configuration::class)
            ->findOneBy(['user' => $user]);
    
        if (!$configuration) {
            // Créer une nouvelle configuration si elle n'existe pas
            $configuration = new Configuration();
            $configuration->setUser($user);
            $entityManager->persist($configuration);
            $entityManager->flush();

             // Assigner le rôle "chauffeur" par défaut
        $chauffeurParam = new Parametre();
        $chauffeurParam->setPropriete('chauffeur');
        $chauffeurParam->setValeur('oui'); // Par défaut, l'utilisateur est chauffeur
        $chauffeurParam->setConfiguration($configuration);

        $entityManager->persist($chauffeurParam);
        $entityManager->flush();
        }
    
        // Charger les paramètres actuels (chauffeur, passager)
        $roles = ['chauffeur' => false, 'passager' => false];
        foreach ($configuration->getParametres() as $parametre) {
            $roles[$parametre->getPropriete()] = $parametre->getValeur() === 'oui';
        }

        // Gestion de la soumission pour modifier les rôles
    if ($request->isMethod('POST')) {
        $chauffeurValue = $request->request->get('chauffeur', 'non');
        $passagerValue = $request->request->get('passager', 'non');

        // Mettre à jour les paramètres dans la configuration
        foreach (['chauffeur' => $chauffeurValue, 'passager' => $passagerValue] as $key => $value) {
            $param = $entityManager->getRepository(Parametre::class)
                ->findOneBy(['propriete' => $key, 'configuration' => $configuration]);

            if (!$param) {
                $param = new Parametre();
                $param->setPropriete($key);
                $param->setConfiguration($configuration);
            }
            $param->setValeur($value);
            $entityManager->persist($param);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Vos paramètres ont été mis à jour.');
        return $this->redirectToRoute('user_dashboard');
    }
    
        // Initialisation des formulaires
        $voitureForm = null;
        $covoiturageForm = null;
    
        // Afficher les formulaires si l'utilisateur est chauffeur
        if (!empty($roles['chauffeur']) && $roles['chauffeur']) {
            // Formulaire pour ajouter un véhicule
            $voiture = new Voiture();
            $voiture->setUser($user);
            $voitureForm = $this->createForm(VoitureType::class, $voiture, [
                'csrf_protection' => true,
            ]);
            $voitureForm->handleRequest($request);
    
            // Formulaire pour ajouter un covoiturage
            $covoiturage = new Covoiturage();
            $covoiturageForm = $this->createForm(CovoiturageType::class, $covoiturage, [
                'user' => $user,
                'csrf_protection' => true,
            ]);
            $covoiturageForm->handleRequest($request);
    
            // Gestion de la soumission du formulaire Voiture
            if ($voitureForm->isSubmitted() && $voitureForm->isValid()) {
                $voiture->setUser($user);
                try {
                    $entityManager->persist($voiture);
                    $entityManager->flush();
                    $this->addFlash('success', 'Le véhicule a été ajouté avec succès.');
                    return $this->redirectToRoute('user_dashboard');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout du véhicule.');
                }
            }
    
            // Gestion de la soumission du formulaire Covoiturage
            if ($covoiturageForm->isSubmitted() && $covoiturageForm->isValid()) {
                $covoiturage->setUser($user);
                $entityManager->persist($covoiturage);
                $entityManager->flush();
                $this->addFlash('success', 'Le covoiturage a été ajouté avec succès.');
            }
        }
    
        // Rendre le tableau de bord
        return $this->render('user/dashboard.html.twig', [
            'roles' => $roles,
            'voitureForm' => $voitureForm ? $voitureForm->createView() : null,
            'covoiturageForm' => $covoiturageForm ? $covoiturageForm->createView() : null,
            'user' => $user,
        ]);
    }
    


    

    #[Route('/voiture/new', name: 'voiture_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser ();
    
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    $voiture = new Voiture();
    $form = $this->createForm(VoitureType::class, $voiture);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $voiture->setUser ($user); // Lier la voiture à l'utilisateur connecté
        $entityManager->persist($voiture);
        $entityManager->flush();

        $this->addFlash('success', 'Le véhicule a été ajouté avec succès.');
        return $this->redirectToRoute('user_dashboard');
    }

    return $this->render('voiture/new.html.twig', [
        'voitureForm' => $form->createView(),
    ]);
}

#[Route('/configuration/update', name: 'configuration_update', methods: ['POST'])]
public function updateConfiguration(
    Request $request,
    EntityManagerInterface $entityManager
): Response {
    // Récupérer la configuration à modifier (par exemple, l'id est passé dans le formulaire)
    $configurationId = $request->request->get('configuration_id');
    $configuration = $entityManager->getRepository(Configuration::class)->find($configurationId);

    if (!$configuration) {
        throw $this->createNotFoundException('Configuration non trouvée.');
    }

    // Récupérer les nouvelles valeurs des paramètres depuis le formulaire
    $chauffeurValue = $request->request->get('chauffeur', 'non');
    $passagerValue = $request->request->get('passager', 'non');

    // Récupérer ou créer les paramètres
    $paramRepository = $entityManager->getRepository(Parametre::class);

    $chauffeurParam = $paramRepository->findOneBy(['propriete' => 'chauffeur', 'configuration' => $configuration]);
    if (!$chauffeurParam) {
        $chauffeurParam = new Parametre();
        $chauffeurParam->setPropriete('chauffeur');
        $chauffeurParam->setConfiguration($configuration);
    }
    $chauffeurParam->setValeur($chauffeurValue);

    $passagerParam = $paramRepository->findOneBy(['propriete' => 'passager', 'configuration' => $configuration]);
    if (!$passagerParam) {
        $passagerParam = new Parametre();
        $passagerParam->setPropriete('passager');
        $passagerParam->setConfiguration($configuration);
    }
    $passagerParam->setValeur($passagerValue);

    // Persister les modifications
    $entityManager->persist($chauffeurParam);
    $entityManager->persist($passagerParam);
    $entityManager->flush();

    $this->addFlash('success', 'Configuration mise à jour avec succès.');

    return $this->redirectToRoute('configuration_dashboard');
}

}