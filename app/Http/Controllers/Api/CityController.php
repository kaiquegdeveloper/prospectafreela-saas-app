<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CityNormalizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CityController extends Controller
{
    /**
     * Busca cidades para autocomplete usando Nominatim
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'User-Agent' => config('app.name', 'Laravel') . '/' . config('app.version', '1.0'),
                ])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $query . ', Brasil',
                    'format' => 'json',
                    'addressdetails' => 1,
                    'limit' => 10,
                    'countrycodes' => 'br',
                    'accept-language' => 'pt-BR',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $cities = [];

                foreach ($data as $item) {
                    $address = $item['address'] ?? [];
                    $city = $address['city'] ?? $address['town'] ?? $address['village'] ?? $address['municipality'] ?? null;
                    $state = $address['state'] ?? null;

                    if ($city) {
                        $displayName = $state ? "{$city}, {$state}" : $city;
                        
                        // Evita duplicatas
                        if (!in_array($displayName, array_column($cities, 'text'))) {
                            $cities[] = [
                                'id' => $displayName,
                                'text' => $displayName,
                                'city' => $city,
                                'state' => $state,
                                'lat' => $item['lat'] ?? null,
                                'lng' => $item['lon'] ?? null,
                            ];
                        }
                    }
                }

                return response()->json($cities);
            }
        } catch (\Exception $e) {
            \Log::warning('Error searching cities', ['error' => $e->getMessage()]);
        }

        return response()->json([]);
    }

    /**
     * Busca cidades próximas usando coordenadas
     */
    public function nearby(Request $request)
    {
        $lat = $request->get('lat');
        $lng = $request->get('lng');
        
        if (!$lat || !$lng) {
            return response()->json([]);
        }

        try {
            // Busca cidades próximas usando uma busca ampla na região
            // Calcula uma bounding box de ~100km ao redor do ponto
            $latOffset = 0.5; // ~55km
            $lngOffset = 0.5; // ~55km (no Brasil)
            
            $response = Http::timeout(5)
                ->withHeaders([
                    'User-Agent' => config('app.name', 'Laravel') . '/' . config('app.version', '1.0'),
                ])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => 'city',
                    'format' => 'json',
                    'addressdetails' => 1,
                    'limit' => 10,
                    'countrycodes' => 'br',
                    'accept-language' => 'pt-BR',
                    'bounded' => 1,
                    'viewbox' => ($lng - $lngOffset) . ',' . ($lat + $latOffset) . ',' . ($lng + $lngOffset) . ',' . ($lat - $latOffset),
                ]);

            $nearbyCities = [];
            if ($response->successful()) {
                $data = $response->json();
                
                foreach ($data as $item) {
                    $address = $item['address'] ?? [];
                    $city = $address['city'] ?? $address['town'] ?? $address['village'] ?? $address['municipality'] ?? null;
                    $state = $address['state'] ?? null;
                    
                    if ($city) {
                        $displayName = $state ? "{$city}, {$state}" : $city;
                        
                        // Evita duplicatas
                        if (!in_array($displayName, array_column($nearbyCities, 'text'))) {
                            $nearbyCities[] = [
                                'text' => $displayName,
                                'city' => $city,
                                'state' => $state,
                            ];
                            
                            // Limita a 4 cidades próximas
                            if (count($nearbyCities) >= 4) {
                                break;
                            }
                        }
                    }
                }
            }

            return response()->json($nearbyCities);
        } catch (\Exception $e) {
            \Log::warning('Error searching nearby cities', ['error' => $e->getMessage()]);
        }

        return response()->json([]);
    }
}

