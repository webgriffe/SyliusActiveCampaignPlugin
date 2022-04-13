<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Resolver;

use Sylius\Component\Core\Model\CustomerInterface;

final class ContactTagsResolver implements ContactTagsResolverInterface
{
    public function resolve(CustomerInterface $customer): array
    {
        return [];
    }
}
