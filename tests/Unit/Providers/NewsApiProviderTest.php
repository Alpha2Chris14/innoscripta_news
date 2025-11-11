<?php

namespace Tests\Unit\Providers;

use App\Services\Providers\NewsApiProvider;

class NewsApiProviderTest extends BaseProviderTest
{
    public function test_fetch_latest_returns_articles_array()
    {
        $responseData = [
            'articles' => [
                [
                    'title' => 'Test Title',
                    'description' => 'Desc',
                    'url' => 'https://example.com/article',
                    'author' => 'Author',
                    'urlToImage' => 'https://example.com/image.jpg',
                    'publishedAt' => now()->toDateTimeString(),
                ],
            ],
        ];

        $mockClient = $this->mockClient($responseData);

        $provider = new NewsApiProvider(['api_key' => 'fake-key']);

        $provider->setHttpClient($mockClient);


        $articles = $provider->fetchLatest();

        $this->assertIsArray($articles);
        $this->assertCount(1, $articles);
        $this->assertArrayHasKey('title', $articles[0]);
        $this->assertEquals('Test Title', $articles[0]['title']);
    }
}
