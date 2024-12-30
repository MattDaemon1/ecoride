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
    #[Route('/', name: 'app_search')]
    public function index(Request $request, CovoiturageRepository $covoiturageRepository): Response
    {
        $form = $this->createForm(SearchCovoiturageType::class);
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

                // Si aucun covoiturage n'est trouvé, chercher les prochains disponibles
                if (empty($covoiturages)) {
                    $upcomingCovoiturages = $covoiturageRepository->findUpcomingResults(
                        $data['lieuDepart'],
                        $data['lieuArrivee']
                    );
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
}