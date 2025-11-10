<?php

namespace App\Services\Providers;

use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;


class NewsApiProvider extends BaseNewsProvider
{
    protected function defaultBaseUrl(): string
    {
        return 'https://newsapi.org/v2/';
    }

    protected function makeRequest(array $options = []): ResponseInterface
    {
        $params = array_merge([
            'apiKey'   => $this->config['api_key'] ?? env('NEWSAPI_KEY'),
            'language' => $options['language'] ?? 'en',
            'pageSize' => $options['pageSize'] ?? 50,
        ], $options['query'] ?? []);

        return $this->http->get('top-headlines', ['query' => $params]);
    }

    protected function transformResponse(array $data, array $options = []): array
    {
        $articles = [];

        foreach (Arr::get($data, 'articles', []) as $item) {
            $articles[] = [
                'external_id'  => null,
                'title'        => $item['title'] ?? '',
                'description'  => $item['description'] ?? null,
                'content'      => $item['content'] ?? null,
                'author'       => $item['author'] ?? null,
                'url'          => $item['url'] ?? '',
                'image_url'    => $item['urlToImage'] ?? null,
                'category'     => $options['category'] ?? null,
                'language'     => $item['language'] ?? 'en',
                'published_at' => $item['publishedAt'] ?? null,
                'meta'         => $item,
            ];
        }

        return $articles;
    }
}
