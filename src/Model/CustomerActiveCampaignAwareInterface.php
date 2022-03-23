<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model;

use Doctrine\Common\Collections\Collection;

interface CustomerActiveCampaignAwareInterface extends ActiveCampaignAwareInterface
{
    /** @return Collection<array-key, ChannelCustomerInterface>|ChannelCustomerInterface[] */
    public function getChannelCustomers();

    public function addChannelCustomer(ChannelCustomerInterface $channelCustomer): void;

    public function removeChannelCustomer(ChannelCustomerInterface $channelCustomer): void;
}
