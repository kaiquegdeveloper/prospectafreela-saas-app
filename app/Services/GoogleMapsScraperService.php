<?php

namespace App\Services;

use App\Models\ApiLog;
use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleMapsScraperService
{
    private CityNormalizationService $cityNormalizer;

    public function __construct(CityNormalizationService $cityNormalizer)
    {
        $this->cityNormalizer = $cityNormalizer;
    }

    /**
     * Busca empresas no Google Maps usando Places API oficial
     *
     * @param string $cidade
     * @param string $nicho
     * @param int|null $userId Para logging e limites
     * @param int|null $limit Limite de resultados (padrão: 50 ou limite do usuário)
     * @param bool $forceNewSearch Se true, força nova busca na API ignorando cache/banco
     * @return array
     */
    public function searchBusinesses(string $cidade, string $nicho, ?int $userId = null, ?int $limit = null, bool $forceNewSearch = false): array
    {
        // Normaliza a cidade usando Nominatim
        $normalizedCity = $this->cityNormalizer->normalizeCity($cidade);
        
        if (!$normalizedCity) {
            Log::warning('City normalization failed', ['cidade' => $cidade]);
            return [];
        }

        // Obtém limite de resultados (padrão: 50)
        if ($limit === null) {
            $limit = $this->getResultLimit($userId);
        }

        // OTIMIZAÇÃO DE CUSTO: Verifica PRIMEIRO no banco de dados (mais confiável que cache)
        // IGNORA se forceNewSearch = true (para buscar NOVOS resultados)
        if (!$forceNewSearch && $userId !== null) {
            $existingSearch = \App\Models\UserSearch::where('user_id', $userId)
                ->where(function ($query) use ($cidade, $normalizedCity) {
                    $query->where('cidade', $cidade)
                          ->orWhere('normalized_cidade', $normalizedCity);
                })
                ->where('nicho', $nicho)
                ->where('status', 'completed')
                ->whereNotNull('raw_data')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($existingSearch && !empty($existingSearch->raw_data)) {
                Log::info('Reusing search from database (ECONOMIZA API)', [
                    'user_id' => $userId,
                    'cidade' => $cidade,
                    'nicho' => $nicho,
                    'search_id' => $existingSearch->id,
                ]);

                $this->logApiCall($userId, 'google_maps_places', '/places/textsearch', 'GET', 200, [
                    'cidade' => $cidade,
                    'normalized_city' => $normalizedCity,
                    'nicho' => $nicho,
                ], ['from_database' => true, 'results_count' => count($existingSearch->raw_data)], 0, true);
                
                return $existingSearch->raw_data;
            }
        }

        // Normaliza cidade e nicho para cache
        $cidadeNormalized = mb_strtolower(trim($normalizedCity));
        $nichoNormalized = mb_strtolower(trim($nicho));
        $cacheKey = "global_search:{$cidadeNormalized}:{$nichoNormalized}:{$limit}";

        // Verifica cache global (90 dias) - SEGUNDA verificação
        // IGNORA se forceNewSearch = true (para buscar NOVOS resultados)
        if (!$forceNewSearch) {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                Log::info('Using cache (ECONOMIZA API)', [
                    'cidade' => $cidade,
                    'nicho' => $nicho,
                ]);

                $this->logApiCall($userId, 'google_maps_places', '/places/textsearch', 'GET', 200, [
                    'cidade' => $cidade,
                    'normalized_city' => $normalizedCity,
                    'nicho' => $nicho,
                ], ['cached' => true, 'results_count' => count($cached)], 0, true);
                
                return $cached;
            }
        }

        // Chama API (força nova busca se forceNewSearch = true, ou se não encontrou cache/banco)
        if ($forceNewSearch) {
            Log::info('Calling Google Maps API (FORCING NEW SEARCH - ignoring cache/database)', [
                'cidade' => $cidade,
                'normalized_city' => $normalizedCity,
                'nicho' => $nicho,
                'user_id' => $userId,
            ]);
        } else {
            Log::info('Calling Google Maps API (no cache found)', [
                'cidade' => $cidade,
                'normalized_city' => $normalizedCity,
                'nicho' => $nicho,
            ]);
        }

        // Monta query: "{nicho} em {cidade_normalizada}"
        $query = "{$nicho} em {$normalizedCity}";

        $startTime = microtime(true);
        $allResults = [];

        try {
            // Google Maps Places API - Text Search
            $apiKey = env('GOOGLE_MAPS_API_KEY');
            
            if (empty($apiKey)) {
                Log::error('Google Maps API key not configured');
                return [];
            }

            $nextPageToken = null;
            $pageCount = 0;
            $maxPages = min(3, ceil($limit / 20)); // Máximo 3 páginas para economizar (60 resultados)

            do {
                $params = [
                    'query' => $query,
                    'key' => $apiKey,
                    'language' => 'pt-BR',
                    'region' => 'br',
                ];

                // Adiciona paginação se houver
                if ($nextPageToken) {
                    $params['pagetoken'] = $nextPageToken;
                    // Aguarda antes de buscar próxima página (requisito da API)
                    sleep(2);
                }

                $response = Http::timeout(30)->get('https://maps.googleapis.com/maps/api/place/textsearch/json', $params);
                $pageCount++;
                $responseTime = (int) ((microtime(true) - $startTime) * 1000);

                if ($response->successful()) {
                    $data = $response->json();

                    if ($data['status'] === 'OK' && isset($data['results'])) {
                        foreach ($data['results'] as $place) {
                            if (count($allResults) >= $limit) {
                                break 2; // Sai dos dois loops
                            }

                            $business = $this->formatPlaceResult($place, $cidade, $nicho);
                            if ($business) {
                                $allResults[] = $business;
                            }
                        }

                        // Verifica se há próxima página
                        $nextPageToken = $data['next_page_token'] ?? null;
                        
                        // OTIMIZAÇÃO DE CUSTO: Para se já temos resultados suficientes
                        // ou atingimos o máximo de páginas ou não há próxima página
                        if (count($allResults) >= $limit || !$nextPageToken || $pageCount >= $maxPages) {
                            break;
                        }
                    } elseif ($data['status'] === 'ZERO_RESULTS') {
                        Log::info('No results found', [
                            'cidade' => $cidade,
                            'nicho' => $nicho,
                            'query' => $query,
                        ]);
                        break;
                    } else {
                        Log::warning('Google Maps Places API error', [
                            'status' => $data['status'] ?? 'UNKNOWN',
                            'error_message' => $data['error_message'] ?? null,
                            'cidade' => $cidade,
                            'nicho' => $nicho,
                        ]);
                        break;
                    }
                } else {
                    $this->logApiCall(
                        $userId,
                        'google_maps_places',
                        '/places/textsearch',
                        'GET',
                        $response->status(),
                        ['cidade' => $cidade, 'nicho' => $nicho, 'query' => $query],
                        ['error' => 'Request failed'],
                        0,
                        false,
                        $responseTime
                    );
                    break;
                }
            } while ($nextPageToken && count($allResults) < $limit && $pageCount < $maxPages);

            // Limita aos resultados solicitados
            $allResults = array_slice($allResults, 0, $limit);

            // Salva no cache global (90 dias) - OTIMIZAÇÃO: Cache agressivo reduz custos
            Cache::put($cacheKey, $allResults, now()->addDays(90));

            // OTIMIZAÇÃO DE CUSTO: Salva também no banco para reutilização futura (mais confiável que cache)
            // Isso garante que mesmo se o cache expirar, os dados estarão no banco
            if ($userId !== null && !empty($allResults)) {
                try {
                    \App\Models\UserSearch::where('user_id', $userId)
                        ->where(function ($query) use ($cidade, $normalizedCity) {
                            $query->where('cidade', $cidade)
                                  ->orWhere('normalized_cidade', $normalizedCity);
                        })
                        ->where('nicho', $nicho)
                        ->where('status', 'pending')
                        ->update([
                            'raw_data' => $allResults,
                            'normalized_cidade' => $normalizedCity,
                        ]);
                } catch (\Exception $e) {
                    Log::warning('Failed to save raw_data to database', [
                        'error' => $e->getMessage(),
                        'user_id' => $userId,
                    ]);
                }
            }

            // OTIMIZAÇÃO DE CUSTO: Text Search custa $0.032 por REQUISIÇÃO, não por resultado
            // Cada página é uma requisição separada
            $cost = $this->calculateApiCost('textsearch', $pageCount);
            $this->logApiCall(
                $userId,
                'google_maps_places',
                '/places/textsearch',
                'GET',
                200,
                [
                    'cidade' => $cidade,
                    'normalized_city' => $normalizedCity,
                    'nicho' => $nicho,
                    'query' => $query,
                ],
                ['results_count' => count($allResults)],
                $cost,
                false,
                $responseTime
            );

            return $allResults;

        } catch (\Exception $e) {
            $responseTime = (int) ((microtime(true) - $startTime) * 1000);
            
            $this->logApiCall(
                $userId,
                'google_maps_places',
                '/places/textsearch',
                'GET',
                500,
                ['cidade' => $cidade, 'nicho' => $nicho],
                ['error' => $e->getMessage()],
                0,
                false,
                $responseTime
            );

            Log::error('Error searching Google Maps Places', [
                'message' => $e->getMessage(),
                'cidade' => $cidade,
                'nicho' => $nicho,
                'trace' => $e->getTraceAsString(),
            ]);

            return [];
        }
    }

    /**
     * Formata resultado do Places API para o formato esperado
     */
    private function formatPlaceResult(array $place, string $cidade, string $nicho): ?array
    {
        try {
            $placeId = $place['place_id'] ?? null;
            $name = $place['name'] ?? null;
            
            if (!$name || !$placeId) {
                return null;
            }

            // Monta URL do Google Maps
            $googleMapsUrl = "https://www.google.com/maps/place/?q=place_id:{$placeId}";

            // Extrai endereço
            $formattedAddress = $place['formatted_address'] ?? null;
            
            // Tenta obter telefone e site via Place Details (com cache)
            // Cache reduz custos - cada place_id é buscado apenas uma vez
            $phone = null;
            $website = null;
            
            // Place Details será chamado no ProcessProspectingJob para economizar
            // (evita chamar para todos os resultados de uma vez)

            return [
                'nome' => $name,
                'google_maps_url' => $googleMapsUrl,
                'place_id' => $placeId,
                'endereco' => $formattedAddress,
                'telefone' => $phone,
                'site' => $website,
                'cidade' => $cidade,
                'nicho' => $nicho,
                'rating' => $place['rating'] ?? null,
                'user_ratings_total' => $place['user_ratings_total'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::warning('Error formatting place result', [
                'error' => $e->getMessage(),
                'place' => $place,
            ]);
            return null;
        }
    }

    /**
     * Obtém detalhes de um lugar usando Place Details API (com cache agressivo)
     * 
     * OTIMIZAÇÃO: Usado apenas quando necessário, com cache de 30 dias
     * Custo: $0.017 por chamada, mas cache reduz drasticamente chamadas repetidas
     *
     * @param string $placeId
     * @param int|null $userId
     * @return array|null
     */
    public function getPlaceDetails(string $placeId, ?int $userId = null): ?array
    {
        $cacheKey = "place_details:{$placeId}";
        $cached = Cache::get($cacheKey);
        
        if ($cached !== null) {
            return $cached;
        }

        try {
            $apiKey = env('GOOGLE_MAPS_API_KEY');
            
            if (empty($apiKey)) {
                return null;
            }

            $response = Http::timeout(15)->get('https://maps.googleapis.com/maps/api/place/details/json', [
                'place_id' => $placeId,
                'key' => $apiKey,
                'language' => 'pt-BR',
                'fields' => 'formatted_phone_number,website', // Apenas campos essenciais para economizar
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['status'] === 'OK' && isset($data['result'])) {
                    $result = $data['result'];
                    
                    $details = [
                        'telefone' => $result['formatted_phone_number'] ?? null,
                        'site' => $result['website'] ?? null,
                    ];

                    // Cache por 30 dias (dados de contato não mudam frequentemente)
                    Cache::put($cacheKey, $details, now()->addDays(30));

                    $cost = $this->calculateApiCost('details');
                    $this->logApiCall(
                        $userId,
                        'google_maps_places',
                        '/places/details',
                        'GET',
                        200,
                        ['place_id' => $placeId],
                        ['success' => true],
                        $cost,
                        false
                    );

                    return $details;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error getting place details', [
                'place_id' => $placeId,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Busca informações adicionais de um site
     *
     * @param string $url
     * @return array
     */
    public function scrapeWebsite(string $url): array
    {
        $data = [
            'email' => null,
            'whatsapp' => null,
            'telefone' => null,
        ];

        try {
            // Normaliza a URL
            if (!str_starts_with($url, 'http')) {
                $url = 'https://' . $url;
            }

            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
            ])->timeout(15)->get($url);

            if ($response->successful()) {
                $html = $response->body();
                
                // Busca email
                $data['email'] = $this->extractEmail($html);
                
                // Busca telefone/WhatsApp
                $phoneData = $this->extractPhoneNumbers($html);
                $data['telefone'] = $phoneData['telefone'] ?? null;
                $data['whatsapp'] = $phoneData['whatsapp'] ?? null;
            }
        } catch (\Exception $e) {
            Log::warning('Error scraping website', [
                'url' => $url,
                'message' => $e->getMessage(),
            ]);
        }

        return $data;
    }

    /**
     * Extrai email do HTML
     */
    private function extractEmail(string $html): ?string
    {
        // Padrão para emails
        $pattern = '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/';
        
        if (preg_match($pattern, $html, $matches)) {
            $email = $matches[0];
            // Filtra emails comuns que não são válidos
            $invalidEmails = ['example.com', 'test.com', 'email.com', 'domain.com'];
            foreach ($invalidEmails as $invalid) {
                if (str_contains($email, $invalid)) {
                    return null;
                }
            }
            return $email;
        }

        return null;
    }

    /**
     * Extrai números de telefone e WhatsApp do HTML
     */
    private function extractPhoneNumbers(string $html): array
    {
        $result = [
            'telefone' => null,
            'whatsapp' => null,
        ];

        // Padrões para telefones brasileiros
        $patterns = [
            // WhatsApp links
            '/wa\.me\/(\d{10,13})/i',
            '/whatsapp\.com\/send\?phone=(\d{10,13})/i',
            // Telefones com DDD
            '/(?:\(?\d{2}\)?\s?)?(?:\d{4,5}[-.\s]?\d{4})/',
            // Telefones sem formatação
            '/(\d{10,11})/',
        ];

        $phones = [];
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $html, $matches)) {
                foreach ($matches[1] ?? $matches[0] ?? [] as $match) {
                    $phone = preg_replace('/\D/', '', $match);
                    if (strlen($phone) >= 10 && strlen($phone) <= 13) {
                        $phones[] = $phone;
                    }
                }
            }
        }

        // Remove duplicatas
        $phones = array_unique($phones);

        if (!empty($phones)) {
            $phone = $phones[0];
            // Formata o telefone
            if (strlen($phone) == 11 && str_starts_with($phone, '55')) {
                $result['whatsapp'] = $phone;
            } elseif (strlen($phone) >= 10) {
                $result['telefone'] = $this->formatPhone($phone);
            }
        }

        return $result;
    }

    /**
     * Formata número de telefone
     */
    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        
        if (strlen($phone) == 11) {
            return sprintf('(%s) %s-%s', substr($phone, 0, 2), substr($phone, 2, 5), substr($phone, 7));
        } elseif (strlen($phone) == 10) {
            return sprintf('(%s) %s-%s', substr($phone, 0, 2), substr($phone, 2, 4), substr($phone, 6));
        }

        return $phone;
    }

    /**
     * Obtém limite de resultados para o usuário
     */
    private function getResultLimit(?int $userId): int
    {
        // Limite padrão: 20 (max_api_fetches)
        $defaultLimit = 20;

        if ($userId === null) {
            return $defaultLimit;
        }

        // Verifica se há limite customizado de API fetches para o usuário
        $user = User::find($userId);
        if ($user) {
            $user->refresh(); // Garante dados atualizados
            $maxApiFetches = $user->getEffectiveMaxApiFetches();
            if ($maxApiFetches !== 20) {
                return $maxApiFetches;
            }
        }

        // Fallback para results_limit (legado) se não tiver max_api_fetches_custom
        if ($user && isset($user->results_limit)) {
            return (int) $user->results_limit;
        }

        // Verifica configuração global
        $globalLimit = AppSetting::get('default_results_limit', $defaultLimit);
        
        return (int) $globalLimit;
    }

    /**
     * Loga chamadas da API
     */
    private function logApiCall(
        ?int $userId,
        string $apiName,
        string $endpoint,
        string $method,
        int $statusCode,
        array $requestData,
        array $responseData,
        float $cost = 0,
        bool $fromCache = false,
        int $responseTime = 0
    ): void {
        try {
            ApiLog::create([
                'user_id' => $userId,
                'api_name' => $apiName,
                'endpoint' => $endpoint,
                'method' => $method,
                'status_code' => $statusCode,
                'request_data' => $requestData,
                'response_data' => array_merge($responseData, ['from_cache' => $fromCache]),
                'cost' => $cost,
                'response_time_ms' => $responseTime,
                'ip_address' => request()->ip() ?? '0.0.0.0',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log API call', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Calcula custo estimado da API
     * 
     * OTIMIZAÇÃO DE CUSTO:
     * - Text Search: $32 por 1.000 requisições = $0.032 por requisição
     * - Cada página de resultados = 1 requisição
     * - Place Details REMOVIDO (economia de 96% nos custos)
     * 
     * Custo por busca: $0.032 (1 página) a $0.096 (3 páginas máximo)
     * vs. $0.85+ se usássemos Place Details para cada resultado
     */
    private function calculateApiCost(string $operation, int $requestCount = 1): float
    {
        // Apenas Text Search é usado (Place Details removido para economizar)
        $costPerRequest = 0.032; // $32 / 1000 = $0.032
        
        return $costPerRequest * $requestCount;
    }
}
