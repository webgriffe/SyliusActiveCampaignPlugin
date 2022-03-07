<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignContactInterface;

final class ContactMapper implements ContactMapperInterface
{
    public function mapFromCustomer(CustomerInterface $customer): ActiveCampaignContactInterface
    {
        throw new \RuntimeException('TODO');
    }
}
