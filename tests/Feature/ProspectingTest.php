<?php

namespace Tests\Feature;

use App\Jobs\ProcessProspectingJob;
use App\Models\ApiLog;
use App\Models\Prospect;
use App\Models\User;
use App\Models\UserSearch;
use App\Services\CityNormalizationService;
use App\Services\GoogleMapsScraperService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class ProspectingTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_blocks_when_quota_is_exceeded(): void
    {
        Queue::fake();

        $user = User::factory()->create([
            'daily_quota_custom' => 1,
            'monthly_quota_custom' => 1,
            'is_active' => true,
        ]);

        Prospect::create([
            'user_id' => $user->id,
            'nome' => 'Cliente Existente',
            'cidade' => 'Curitiba',
            'nicho' => 'Dentista',
            'status' => 'done',
        ]);

        $response = $this->actingAs($user)->post(route('prospects.store'), [
            'cidade' => 'Curitiba',
            'nicho' => 'Dentista',
            'max_results' => 1,
        ]);

        $response->assertRedirect(route('prospects.create'));
        $response->assertSessionHasErrors('quota');

        Queue::assertNothingPushed();
    }

    public function test_store_reuses_existing_search_without_new_records(): void
    {
        Queue::fake();

        $user = User::factory()->create([
            'is_active' => true,
            'max_api_fetches_custom' => 15,
        ]);

        UserSearch::create([
            'user_id' => $user->id,
            'cidade' => 'São Paulo',
            'normalized_cidade' => 'São Paulo, Brasil',
            'nicho' => 'Dentista',
            'status' => 'completed',
            'raw_data' => [
                ['nome' => 'Empresa A', 'google_maps_url' => 'https://maps/empresa-a'],
            ],
        ]);

        $response = $this->actingAs($user)->post(route('prospects.store'), [
            'cidade' => 'São Paulo',
            'nicho' => 'Dentista',
        ]);

        $response->assertRedirect(route('prospects.index'));
        $response->assertSessionHas('info');

        Queue::assertPushed(ProcessProspectingJob::class, function (ProcessProspectingJob $job) use ($user) {
            return $job->userId === $user->id
                && $job->cidade === 'São Paulo'
                && $job->nicho === 'Dentista'
                && $job->maxResults === $user->getEffectiveMaxApiFetches();
        });

        $this->assertSame(1, UserSearch::count(), 'Nenhuma nova pesquisa deve ser criada ao reutilizar dados');
    }

    public function test_prospecting_job_stops_when_quota_is_hit_mid_processing(): void
    {
        // Configura fake para interceptar requisições HTTP (incluindo isValidSite)
        Http::fake([
            '*' => Http::response('', 200),
        ]);

        $user = User::factory()->create([
            'daily_quota_custom' => 1,
            'monthly_quota_custom' => 1,
            'max_api_fetches_custom' => 2,
            'is_active' => true,
        ]);

        $cityNormalizer = Mockery::mock(CityNormalizationService::class);
        $cityNormalizer->shouldReceive('normalizeCity')
            ->once()
            ->with('Curitiba')
            ->andReturn('Curitiba, Brasil');

        $scraper = Mockery::mock(GoogleMapsScraperService::class);
        $scraper->shouldReceive('searchBusinesses')
            ->once()
            ->with('Curitiba', 'Dentista', $user->id, 2)
            ->andReturn([
                [
                    'nome' => 'Primeiro Cliente',
                    'google_maps_url' => 'https://maps/primeiro',
                    'place_id' => 'place-1',
                    'endereco' => 'Rua 1',
                    'cidade' => 'Curitiba',
                    'nicho' => 'Dentista',
                ],
                [
                    'nome' => 'Segundo Cliente',
                    'google_maps_url' => 'https://maps/segundo',
                    'place_id' => 'place-2',
                    'endereco' => 'Rua 2',
                    'cidade' => 'Curitiba',
                    'nicho' => 'Dentista',
                ],
            ]);

        $scraper->shouldReceive('getPlaceDetails')
            ->once()
            ->with('place-1', $user->id)
            ->andReturn([
                'telefone' => '55999999999',
                'site' => 'https://primeiro.com',
            ]);

        $scraper->shouldReceive('scrapeWebsite')
            ->once()
            ->with('https://primeiro.com')
            ->andReturn([
                'email' => 'contato@primeiro.com',
                'whatsapp' => '55999999999',
                'telefone' => '55999999999',
            ]);

        $job = new ProcessProspectingJob($user->id, 'Curitiba', 'Dentista', 2);
        $job->handle($scraper, $cityNormalizer);

        $search = UserSearch::first();
        $this->assertNotNull($search, 'A pesquisa deve ser registrada');
        $this->assertEquals('failed', $search->status);
        $this->assertEquals(1, $search->results_count);

        $this->assertEquals(1, Prospect::count(), 'Deve parar ao atingir a cota');

        // isValidSite faz uma requisição HEAD, então esperamos 1 requisição
        Http::assertSentCount(1);
    }

    public function test_google_maps_service_reuses_database_results(): void
    {
        Http::fake();

        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $rawData = [
            [
                'nome' => 'Empresa Cacheada',
                'google_maps_url' => 'https://maps/cacheada',
                'place_id' => 'cache-1',
                'endereco' => 'Rua das Flores',
                'cidade' => 'São Paulo',
                'nicho' => 'Dentista',
            ],
        ];

        UserSearch::create([
            'user_id' => $user->id,
            'cidade' => 'São Paulo',
            'normalized_cidade' => 'São Paulo, Brasil',
            'nicho' => 'Dentista',
            'status' => 'completed',
            'raw_data' => $rawData,
        ]);

        $cityNormalizer = Mockery::mock(CityNormalizationService::class);
        $cityNormalizer->shouldReceive('normalizeCity')
            ->once()
            ->with('São Paulo')
            ->andReturn('São Paulo, Brasil');

        $service = new GoogleMapsScraperService($cityNormalizer);

        $result = $service->searchBusinesses('São Paulo', 'Dentista', $user->id, 5);

        $this->assertSame($rawData, $result);
        $this->assertDatabaseCount('api_logs', 1);

        $apiLog = ApiLog::first();
        $this->assertTrue($apiLog->response_data['from_cache']);
        $this->assertTrue($apiLog->response_data['from_database']);
        $this->assertEquals(1, $apiLog->response_data['results_count']);

        Http::assertSentCount(0);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

