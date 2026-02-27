<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomer as BaseChannelCustomer;

#[ORM\Entity]
#[ORM\Table(name: 'webgriffe_sylius_active_campaign_channel_customer')]
#[ORM\UniqueConstraint(name: 'channel_customer_idx', columns: ['channel_id', 'customer_id'])]
class ChannelCustomer extends BaseChannelCustomer
{
}
