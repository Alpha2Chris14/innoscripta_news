<?php

namespace App\Services\Providers;

use App\Contracts\NewsProviderInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;

class NytProvider implements NewsProviderInterface
{
    protected Client $http;
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->http = new Client([
            'base_uri' => $config['base_url'] ?? 'https://api.nytimes.com/svc/topstories/v2/',
            'timeout'  => $config['timeout'] ?? 10,
        ]);
    }

    public function fetchLatest(array $options = []): array
    {
        $section = $options['section'] ?? 'home';
        $params = [
            'api-key' => $this->config['api_key'] ?? env('NYT_KEY'),
        ];

        $response = $this->http->get("{$section}.json", ['query' => $params]);
        $data = json_decode((string)$response->getBody(), true);

        $articles = [];
        foreach (Arr::get($data, 'results', []) as $item) {
            $articles[] = [
                'external_id' => $item['url'] ?? null,
                'title'       => $item['title'] ?? '',
                'description' => $item['abstract'] ?? null,
                'content'     => null,
                'author'      => $item['byline'] ?? null,
                'url'         => $item['url'] ?? '',
                'image_url'   => Arr::first($item['multimedia'] ?? [], ['url' => null])['url'] ?? null,
                'category'    => $item['section'] ?? null,
                'language'    => 'en',
                'published_at' => $item['published_date'] ?? null,
                'meta'        => $item,
            ];
        }

        return $articles;
    }
}
