<?php

namespace Tests\Unit\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Mockery;

abstract class BaseProviderTest extends TestCase
{
    protected function mockClient(array $responseData): Client
    {
        $mock = Mockery::mock(Client::class);
        $mock->shouldReceive('get')
            ->andReturn(new Response(200, [], json_encode($responseData)));
        return $mock;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
