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
        
        try {
            $response = $this->httpClient->request(
                'GET',
                'https://api-adresse.data.gouv.fr/search/',
                [
                    'query' => [
                        'q' => $query,
                        'type' => 'municipality',
                        'limit' => 8,
                        'autocomplete' => 1
                    ]
                ]
            );

            $data = json_decode($response->getContent(), true);
            
            // Filtrer uniquement les villes françaises et formater les résultats
            $suggestions = array_values(array_filter(array_map(function($feature) {
                $properties = $feature['properties'];
                // Vérifier que le citycode commence par un numéro de département français
                if (preg_match('/^([0-9]{2}|2[AB]|97[1-6]|98[4-9]|99)/', $properties['citycode'])) {
                    $context = isset($properties['context']) ? explode(', ', $properties['context'])[1] : '';
                    return [
                        'label' => sprintf('%s (%s) - %s', 
                            $properties['city'],
                            $properties['postcode'],
                            $context
                        ),
                        'value' => $properties['city'],
                        'postcode' => $properties['postcode'],
                        'department' => $context,
                        'coordinates' => $feature['geometry']['coordinates']
                    ];
                }
                return null;
            }, $data['features'])));

            return new JsonResponse($suggestions);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Une erreur est survenue lors de la recherche',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}