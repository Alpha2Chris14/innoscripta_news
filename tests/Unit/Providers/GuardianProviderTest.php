<?php

namespace Tests\Unit\Providers;

use App\Services\Providers\GuardianProvider;

class GuardianProviderTest extends BaseProviderTest
{
    public function test_fetch_latest_returns_articles_array()
    {
        $responseData = [
            'response' => [
                'results' => [
                    [
                        'id' => 'guardian-001',
                        'webTitle' => 'Guardian Article',
                        'webUrl' => 'https://theguardian.com/test',
                        'sectionName' => 'News',
                        'webPublicationDate' => now()->toDateTimeString(),
                    ],
                ],
            ],
        ];

        $mockClient = $this->mockClient($responseData);

        $provider = new GuardianProvider(['api_key' => 'fake-key']);
        $provider->setHttpClient($mockClient);
        $articles = $provider->fetchLatest();

        $this->assertIsArray($articles);
        $this->assertEquals('Guardian Article', $articles[0]['title']);
    }
}
