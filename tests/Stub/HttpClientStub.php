<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

final class HttpClientStub implements ClientInterface
{
    public static int $responseStatusCode = 200;

    public static ?string $responseBodyContent = null;

    /** @var RequestInterface[] */
    public static array $sentRequests = [];

    public static function setUp(): void
    {
        self::$sentRequests = [];
    }

    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        self::$sentRequests[] = $request;

        return new Response(self::$responseStatusCode, [], self::$responseBodyContent);
    }

    public function sendAsync(RequestInterface $request, array $options = []): PromiseInterface
    {
        throw new RuntimeException('Not implemented');
    }

    public function request($method, $uri, array $options = []): ResponseInterface
    {
        throw new RuntimeException('Not implemented');
    }

    public function requestAsync($method, $uri, array $options = []): PromiseInterface
    {
        throw new RuntimeException('Not implemented');
    }

    public function getConfig($option = null): void
    {
        throw new RuntimeException('Not implemented');
    }
}
