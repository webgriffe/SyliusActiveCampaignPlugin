<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;

interface CustomerActiveCampaignAwareInterface extends ActiveCampaignAwareInterface
{
    /** @return Collection<array-key, ChannelCustomerInterface>|ChannelCustomerInterface[] */
    public function getChannelCustomers();

    public function getChannelCustomerByChannel(ChannelInterface $channel): ?ChannelCustomerInterface;

    public function addChannelCustomer(ChannelCustomerInterface $channelCustomer): void;

    public function removeChannelCustomer(ChannelCustomerInterface $channelCustomer): void;
}
