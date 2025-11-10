<?php

namespace App\Services\Providers;

use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;

class NytProvider extends BaseNewsProvider
{
    protected function defaultBaseUrl(): string
    {
        return 'https://api.nytimes.com/svc/topstories/v2/';
    }

    protected function makeRequest(array $options = []): ResponseInterface
    {
        $section = $options['section'] ?? 'home';

        $params = [
            'api-key' => $this->config['api_key'] ?? env('NYT_KEY'),
        ];

        return $this->http->get("{$section}.json", ['query' => $params]);
    }

    protected function transformResponse(array $data, array $options = []): array
    {
        $articles = [];

        foreach (Arr::get($data, 'results', []) as $item) {
            $image = Arr::first($item['multimedia'] ?? [])['url'] ?? null;

            $articles[] = [
                'external_id'  => $item['url'] ?? null,
                'title'        => $item['title'] ?? '',
                'description'  => $item['abstract'] ?? null,
                'content'      => null,
                'author'       => $item['byline'] ?? null,
                'url'          => $item['url'] ?? '',
                'image_url'    => $image,
                'category'     => $item['section'] ?? null,
                'language'     => 'en',
                'published_at' => $item['published_date'] ?? null,
                'meta'         => $item,
            ];
        }

        return $articles;
    }
}
