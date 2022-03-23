<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

/** @psalm-suppress MissingConstructor */
class ChannelCustomer implements ChannelCustomerInterface
{
    /** @var mixed */
    protected $id;

    protected ChannelInterface $channel;

    protected CustomerInterface $customer;

    protected int $activeCampaignId;

    /** @return mixed */
    public function getId()
    {
        return $this->id;
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

    public function setCustomer(CustomerInterface $customer): void
    {
        $this->customer = $customer;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    public function getActiveCampaignId(): int
    {
        return $this->activeCampaignId;
    }

    public function setActiveCampaignId(int $activeCampaignId): void
    {
        $this->activeCampaignId = $activeCampaignId;
    }
}
