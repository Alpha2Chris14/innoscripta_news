<?php

namespace Tests\Unit\Providers;

use App\Services\Providers\NytProvider;

class NytProviderTest extends BaseProviderTest
{
    public function test_fetch_latest_returns_articles_array()
    {
        $responseData = [
            'results' => [
                [
                    'title' => 'NYT Article',
                    'url' => 'https://nytimes.com/test',
                    'abstract' => 'An NYT story',
                    'byline' => 'John Doe',
                    'section' => 'World',
                    'published_date' => now()->toDateTimeString(),
                ],
            ],
        ];

        $mockClient = $this->mockClient($responseData);

        $provider = new NytProvider(['api_key' => 'fake-key']);
        $provider->setHttpClient($mockClient);


        $articles = $provider->fetchLatest();

        $this->assertIsArray($articles);
        $this->assertEquals('NYT Article', $articles[0]['title']);
    }
}
