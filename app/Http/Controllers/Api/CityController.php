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
}

