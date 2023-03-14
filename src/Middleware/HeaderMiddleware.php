<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Middleware;

use Psr\Http\Message\RequestInterface;

final class HeaderMiddleware
{
    public function __construct(
        private string $apiKey,
    ) {
    }

    /**
     * Called when the middleware is handled by the client.
     */
    public function __invoke(callable $handler): callable
    {
        /** @psalm-suppress MissingClosureReturnType */
        return function (
            RequestInterface $request,
            array $options,
        ) use ($handler) {
            $request = $request
                ->withHeader('Api-Token', $this->apiKey)
                ->withHeader('Accept', 'application/json')
                ->withHeader('Content-Type', 'application/json')
            ;

            return $handler($request, $options);
        };
    }
}
