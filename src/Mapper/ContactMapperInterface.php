<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignContactInterface;

interface ContactMapperInterface
{
    public function mapFromCustomer(CustomerInterface $customer): ActiveCampaignContactInterface;
}
