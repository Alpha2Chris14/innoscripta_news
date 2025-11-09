<?php

namespace App\Services\Providers;

use App\Contracts\NewsProviderInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;

class NewsApiProvider implements NewsProviderInterface
{
    protected Client $http;
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->http = new Client([
            'base_uri' => $config['base_url'] ?? 'https://newsapi.org/v2/',
            'timeout' => 10,
        ]);
    }

    public function fetchLatest(array $options = []): array
    {
        $params = array_merge([
            'apiKey' => $this->config['api_key'] ?? env('NEWSAPI_KEY'),
            'language' => $options['language'] ?? 'en',
            'pageSize' => $options['pageSize'] ?? 50,
        ], $options['query'] ?? []);

        $resp = $this->http->get('top-headlines', ['query' => $params]);
        $data = json_decode((string)$resp->getBody(), true);

        $articles = [];
        foreach (Arr::get($data, 'articles', []) as $item) {
            $articles[] = [
                'external_id' => null, // NewsAPI doesn't always provide id so I set null if not available
                'title' => $item['title'] ?? null,
                'description' => $item['description'] ?? null,
                'content' => $item['content'] ?? null,
                'author' => $item['author'] ?? null,
                'url' => $item['url'],
                'image_url' => $item['urlToImage'] ?? null,
                'category' => $options['category'] ?? null,
                'language' => $item['language'] ?? $params['language'] ?? 'en',
                'published_at' => $item['publishedAt'] ?? null,
                'meta' => $item,
            ];
        }

        return $articles;
    }
}
