<?php

namespace App\Controller;

use App\Form\SearchCovoiturageType;
use App\Repository\CovoiturageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index(Request $request, CovoiturageRepository $covoiturageRepository): Response
    {
        $form = $this->createForm(SearchCovoiturageType::class, null, [
            'method' => 'GET'
        ]);
        
        $form->handleRequest($request);
        $covoiturages = [];
        $upcomingCovoiturages = [];
        $searchCriteria = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $searchCriteria = [
                'lieuDepart' => $data['lieuDepart'],
                'lieuArrivee' => $data['lieuArrivee'],
                'dateDepart' => $data['dateDepart']
            ];

            try {
                $covoiturages = $covoiturageRepository->findByCriteria(
                    $data['lieuDepart'],
                    $data['lieuArrivee'],
                    $data['dateDepart']
                );

                if (empty($covoiturages)) {
                    $upcomingCovoiturages = $covoiturageRepository->findUpcomingResults(
                        $data['lieuDepart'],
                        $data['lieuArrivee']
                    );

                    if (!empty($upcomingCovoiturages)) {
                        $this->addFlash(
                            'info',
                            'Aucun covoiturage trouvé à cette date. Voici les prochains trajets disponibles.'
                        );
                    } else {
                        $this->addFlash(
                            'warning',
                            'Aucun covoiturage disponible pour ce trajet.'
                        );
                    }
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la recherche. Veuillez réessayer.');
            }
        }

        return $this->render('search/index.html.twig', [
            'form' => $form->createView(),
            'covoiturages' => $covoiturages,
            'upcomingCovoiturages' => $upcomingCovoiturages,
            'searchCriteria' => $searchCriteria
        ]);
    }


    #[Route('/covoiturage_detail', name: 'app_covoiturage_detail')]
    public function contact(): Response
    {
        return $this->render('search/covoiturage_detail.html.twig', [
            'controller_name' => 'SearchController',
        ]);
    }
}