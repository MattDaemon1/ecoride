<?php

namespace App\Controller;

use App\Form\SearchCovoiturageType;
use App\Repository\CovoiturageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/search/filter', name: 'search_filter', methods: ['POST'])]
    public function filter(Request $request, CovoiturageRepository $repository): JsonResponse
    {
        $filters = $request->request->all();
        $criteria = [
            'prix' => $filters['prixMax'] ?? null,
            'duree' => $filters['dureeMax'] ?? null,
            'ecologique' => $filters['ecologique'] ?? false,
            'note' => $filters['noteMinimale'] ?? null,
        ];

        $results = $repository->findByFilterCriteria($criteria);

        return $this->json($results);
    }

    public function results(Request $request, CovoiturageRepository $repository): Response
{
    $filters = [
        'prixMax' => $request->query->get('prixMax'),
        'dureeMax' => $request->query->get('dureeMax'),
        'ecologique' => $request->query->get('ecologique'),
        'noteMinimale' => $request->query->get('noteMinimale'),
    ];

    $covoiturages = $repository->findFilteredCovoiturages($filters);

    return $this->render('_partials/_results.html.twig', [
        'covoiturages' => $covoiturages,
    ]);
}




#[Route('/covoiturage/{id}/detail', name: 'app_covoiturage_detail')]
public function detail(int $id, CovoiturageRepository $repository): Response
{
    $covoiturage = $repository->find($id);
    if (!$covoiturage) {
        throw $this->createNotFoundException('Covoiturage introuvable.');
    }

    return $this->render('covoiturage/detail.html.twig', [
        'covoiturage' => $covoiturage,
    ]);
}

}
