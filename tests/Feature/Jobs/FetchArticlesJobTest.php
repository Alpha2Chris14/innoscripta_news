<?php

namespace Tests\Feature\Jobs;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Source;
use App\Jobs\FetchArticlesJob;
use Illuminate\Support\Facades\Bus;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use App\Services\Providers\NewsApiProvider;

class FetchArticlesJobTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function job_processes_articles_successfully()
    {
        Bus::fake();

        // Create a fake source
        $source = Source::factory()->create([
            'slug'    => 'newsapi',
            'name'    => 'NewsAPI',
            'config'  => [
                'provider_class' => NewsApiProvider::class,
                'api_key'        => 'fake-key',
                'base_url'       => 'https://newsapi.org/v2/',
            ],
            'enabled' => true,
        ]);

        // Mock Guzzle response
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'articles' => [
                    [
                        'title' => 'Test Article',
                        'description' => 'Test Description',
                        'url' => 'https://example.com/article',
                        'publishedAt' => now()->toISOString(),
                    ],
                ],
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Inject mocked client into provider
        $providerClass = $source->config['provider_class'];
        $provider = new $providerClass($source->config);
        $provider->setHttpClient($client);

        // Dispatch the job with the source ID
        FetchArticlesJob::dispatch($source->id);

        // Run the job manually to simulate processing
        $job = new FetchArticlesJob($source->id);
        $job->handleWithProvider($provider); // Use the provider with mocked HTTP

        // Assert the articles exist in the database
        $this->assertDatabaseHas('articles', [
            'source_id' => $source->id,
            'title' => 'Test Article',
        ]);
    }
}
