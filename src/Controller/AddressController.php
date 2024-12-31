<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AddressController extends AbstractController
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/api/address-search', name: 'api_address_search')]
    public function searchAddress(Request $request): JsonResponse
    {
        $query = $request->query->get('q');
        $type = 'municipality'; // Pour ne retourner que les villes

        try {
            $response = $this->httpClient->request(
                'GET',
                'https://api-adresse.data.gouv.fr/search/',
                [
                    'query' => [
                        'q' => $query,
                        'type' => $type,
                        'limit' => 5
                    ]
                ]
            );

            $data = json_decode($response->getContent(), true);
            
            // Formatter les résultats
            $suggestions = array_map(function($feature) {
                return [
                    'label' => $feature['properties']['city'],
                    'value' => $feature['properties']['city'],
                ];
            }, $data['features']);

            return new JsonResponse($suggestions);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Une erreur est survenue'], 500);
        }
    }
}