<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface ChannelCustomerInterface extends ResourceInterface
{
    public function getCustomer(): CustomerInterface;

    public function setCustomer(CustomerInterface $customer): void;

    public function getChannel(): ChannelInterface;

    public function setChannel(ChannelInterface $channel): void;

    public function getActiveCampaignId(): int;

    public function setActiveCampaignId(int $activeCampaignId): void;

    public function getListSubscriptionStatus(): ?int;

    public function setListSubscriptionStatus(?int $listSubscriptionStatus): void;
}
