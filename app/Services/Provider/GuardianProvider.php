<?php

namespace App\Services\Providers;

use App\Contracts\NewsProviderInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;

class GuardianProvider implements NewsProviderInterface
{
    protected Client $http;
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->http = new Client([
            'base_uri' => $config['base_url'] ?? 'https://content.guardianapis.com/',
            'timeout'  => $config['timeout'] ?? 10,
        ]);
    }

    public function fetchLatest(array $options = []): array
    {
        $params = array_merge([
            'api-key' => $this->config['api_key'] ?? env('GUARDIAN_KEY'),
            'page-size' => $options['pageSize'] ?? 50,
            'order-by' => $options['order_by'] ?? 'newest',
        ], $options['query'] ?? []);

        $response = $this->http->get('search', ['query' => $params]);
        $data = json_decode((string)$response->getBody(), true);

        $articles = [];
        foreach (Arr::get($data, 'response.results', []) as $item) {
            $articles[] = [
                'external_id' => $item['id'] ?? null,
                'title'       => $item['webTitle'] ?? '',
                'description' => null,
                'content'     => null,
                'author'      => $item['fields']['byline'] ?? null,
                'url'         => $item['webUrl'] ?? '',
                'image_url'   => $item['fields']['thumbnail'] ?? null,
                'category'    => $item['sectionName'] ?? null,
                'language'    => $item['fields']['language'] ?? null,
                'published_at' => $item['webPublicationDate'] ?? null,
                'meta'        => $item,
            ];
        }

        return $articles;
    }
}
