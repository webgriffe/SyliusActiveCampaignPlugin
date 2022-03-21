<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
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

    public function send(RequestInterface $request, array $options = [])
    {
        self::$sentRequests[] = $request;
        return new Response(self::$responseStatusCode, [], self::$responseBodyContent);
    }

    public function sendAsync(RequestInterface $request, array $options = [])
    {
        throw new RuntimeException('Not implemented');
    }

    public function request($method, $uri, array $options = [])
    {
        throw new RuntimeException('Not implemented');
    }

    public function requestAsync($method, $uri, array $options = [])
    {
        throw new RuntimeException('Not implemented');
    }

    public function getConfig($option = null)
    {
        throw new RuntimeException('Not implemented');
    }
}
