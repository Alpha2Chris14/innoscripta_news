<?php

namespace App\Services\Providers;

use App\Contracts\NewsProviderInterface;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

abstract class BaseNewsProvider implements NewsProviderInterface
{
    protected Client $http;
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;

        $this->http = new Client([
            'base_uri' => $config['base_url'] ?? $this->defaultBaseUrl(),
            'timeout'  => $config['timeout'] ?? 10,
        ]);
    }

    /**
     * Each provider defines its default base URL.
     */
    abstract protected function defaultBaseUrl(): string;

    /**
     * Each provider defines how to fetch its data from API.
     */
    abstract protected function makeRequest(array $options = []): ResponseInterface;

    /**
     * Each provider defines how to transform its API response.
     */
    abstract protected function transformResponse(array $data, array $options = []): array;

    /**
     * Generic method used by all providers to fetch latest news.
     */
    public function fetchLatest(array $options = []): array
    {
        $response = $this->makeRequest($options);
        $data = json_decode((string) $response->getBody(), true);

        return $this->transformResponse($data, $options);
    }
}
