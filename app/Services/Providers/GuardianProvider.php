<?php

namespace App\Services\Providers;

use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;


class GuardianProvider extends BaseNewsProvider
{
    protected function defaultBaseUrl(): string
    {
        return 'https://content.guardianapis.com/';
    }

    protected function makeRequest(array $options = []): ResponseInterface
    {
        $params = array_merge([
            'api-key'   => $this->config['api_key'] ?? env('GUARDIAN_KEY'),
            'page-size' => $options['pageSize'] ?? 50,
            'order-by'  => $options['order_by'] ?? 'newest',
        ], $options['query'] ?? []);

        return $this->http->get('search', ['query' => $params]);
    }

    protected function transformResponse(array $data, array $options = []): array
    {
        $articles = [];

        foreach (Arr::get($data, 'response.results', []) as $item) {
            $articles[] = [
                'external_id'  => $item['id'] ?? null,
                'title'        => $item['webTitle'] ?? '',
                'description'  => null,
                'content'      => null,
                'author'       => $item['fields']['byline'] ?? null,
                'url'          => $item['webUrl'] ?? '',
                'image_url'    => $item['fields']['thumbnail'] ?? null,
                'category'     => $item['sectionName'] ?? null,
                'language'     => $item['fields']['language'] ?? 'en',
                'published_at' => $item['webPublicationDate'] ?? null,
                'meta'         => $item,
            ];
        }

        return $articles;
    }
}
