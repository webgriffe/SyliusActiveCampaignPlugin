<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

/**
 * @psalm-api
 */
abstract class AbstractFactory
{
    /**
     * @template T of object
     *
     * @psalm-param class-string<T> $targetClassFQCN
     */
    public function __construct(
        protected string $targetClassFQCN,
    ) {
    }
}
