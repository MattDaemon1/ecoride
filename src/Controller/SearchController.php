<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search_route", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        // Logique de recherche à implémenter ici
        $departure = $request->query->get('lieuDepart');
        $arrival = $request->query->get('lieuArrivee');
        $date = $request->query->get('dateDepart');

        // Vous pouvez rendre une vue ou retourner une réponse JSON
        return $this->render('search/index.html.twig', [
            'departure' => $departure,
            'arrival' => $arrival,
            'date' => $date,
        ]);
    }
}
