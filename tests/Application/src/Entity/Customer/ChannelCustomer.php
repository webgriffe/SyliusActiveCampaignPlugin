<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomer as BaseChannelCustomer;

/**
 * @ORM\Entity
 * @ORM\Table(name="webgriffe_sylius_active_campaign_channel_customer")
 */
class ChannelCustomer extends BaseChannelCustomer
{
}
