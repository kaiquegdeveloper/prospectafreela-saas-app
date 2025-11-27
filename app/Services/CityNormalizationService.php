<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CityNormalizationService
{
    /**
     * Normaliza o nome da cidade usando Nominatim (OpenStreetMap)
     * Retorna o nome padronizado: "Cidade, Estado, País"
     *
     * @param string $cidade
     * @return string|null Nome normalizado ou null se não encontrado
     */
    public function normalizeCity(string $cidade): ?string
    {
        // Normaliza entrada
        $cidade = trim($cidade);
        
        if (empty($cidade)) {
            return null;
        }

        // Cache por 30 dias (cidades não mudam frequentemente)
        $cacheKey = "city_normalization:" . mb_strtolower($cidade);
        $cached = Cache::get($cacheKey);
        
        if ($cached !== null) {
            return $cached;
        }

        try {
            // Busca no Nominatim com foco no Brasil
            $response = Http::timeout(10)->get('https://nominatim.openstreetmap.org/search', [
                'q' => $cidade . ', Brasil',
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => 1,
                'countrycodes' => 'br',
                'accept-language' => 'pt-BR',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data) && isset($data[0])) {
                    $result = $data[0];
                    $address = $result['address'] ?? [];
                    
                    // Monta nome padronizado: Cidade, Estado, Brasil
                    $normalized = $this->buildNormalizedName($address, $cidade);
                    
                    if ($normalized) {
                        // Salva no cache por 30 dias
                        Cache::put($cacheKey, $normalized, now()->addDays(30));
                        
                        Log::info('City normalized', [
                            'original' => $cidade,
                            'normalized' => $normalized,
                        ]);
                        
                        return $normalized;
                    }
                }
            }

            Log::warning('City not found in Nominatim', [
                'cidade' => $cidade,
                'status' => $response->status(),
            ]);

            // Se não encontrou, retorna a cidade original com "Brasil" adicionado
            $fallback = $cidade . ', Brasil';
            Cache::put($cacheKey, $fallback, now()->addDays(30));
            
            return $fallback;

        } catch (\Exception $e) {
            Log::error('Error normalizing city', [
                'cidade' => $cidade,
                'error' => $e->getMessage(),
            ]);

            // Fallback: retorna cidade original com "Brasil"
            $fallback = $cidade . ', Brasil';
            Cache::put($cacheKey, $fallback, now()->addDays(1)); // Cache menor em caso de erro
            
            return $fallback;
        }
    }

    /**
     * Constrói o nome normalizado a partir do endereço retornado pelo Nominatim
     */
    private function buildNormalizedName(array $address, string $originalCity): ?string
    {
        $city = $address['city'] ?? $address['town'] ?? $address['village'] ?? $address['municipality'] ?? null;
        $state = $address['state'] ?? null;
        $country = $address['country'] ?? 'Brasil';

        // Se não encontrou cidade, usa o nome original
        if (!$city) {
            $city = $originalCity;
        }

        // Formata: Cidade, Estado, Brasil
        $parts = array_filter([$city, $state, $country]);
        
        return !empty($parts) ? implode(', ', $parts) : null;
    }

    /**
     * Obtém coordenadas (lat, lng) da cidade
     *
     * @param string $cidade
     * @return array|null ['lat' => float, 'lng' => float] ou null
     */
    public function getCityCoordinates(string $cidade): ?array
    {
        $normalized = $this->normalizeCity($cidade);
        
        if (!$normalized) {
            return null;
        }

        $cacheKey = "city_coordinates:" . mb_strtolower($normalized);
        $cached = Cache::get($cacheKey);
        
        if ($cached !== null) {
            return $cached;
        }

        try {
            $response = Http::timeout(10)->get('https://nominatim.openstreetmap.org/search', [
                'q' => $normalized,
                'format' => 'json',
                'limit' => 1,
                'countrycodes' => 'br',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data) && isset($data[0])) {
                    $result = $data[0];
                    
                    if (isset($result['lat']) && isset($result['lon'])) {
                        $coordinates = [
                            'lat' => (float) $result['lat'],
                            'lng' => (float) $result['lon'],
                        ];
                        
                        Cache::put($cacheKey, $coordinates, now()->addDays(30));
                        
                        return $coordinates;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error getting city coordinates', [
                'cidade' => $cidade,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }
}

