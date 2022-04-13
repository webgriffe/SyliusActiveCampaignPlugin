<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Resolver;

use Sylius\Component\Core\Model\CustomerInterface;

interface ContactTagsResolverInterface
{
    /** @return string[] */
    public function resolve(CustomerInterface $customer): array;
}
