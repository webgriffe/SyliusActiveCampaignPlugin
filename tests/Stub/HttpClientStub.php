<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use RuntimeException;

final class HttpClientStub implements ClientInterface
{
    public static int $responseStatusCode = 201;

    public static ?string $responseBodyContent = null;

    public function send(RequestInterface $request, array $options = [])
    {
        return new Response(self::$responseStatusCode, [], self::$responseBodyContent);
    }

    public function sendAsync(RequestInterface $request, array $options = [])
    {
        throw new RuntimeException('not implemented yet');
    }

    public function request($method, $uri, array $options = [])
    {
        throw new RuntimeException('not implemented yet');
    }

    public function requestAsync($method, $uri, array $options = [])
    {
        throw new RuntimeException('not implemented yet');
    }

    public function getConfig($option = null)
    {
        throw new RuntimeException('not implemented yet');
    }
}
