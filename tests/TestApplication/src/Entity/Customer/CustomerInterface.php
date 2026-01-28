<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Entity\Customer;

use Sylius\Component\Core\Model\CustomerInterface as BaseCustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface;

interface CustomerInterface extends BaseCustomerInterface, CustomerActiveCampaignAwareInterface
{
}
