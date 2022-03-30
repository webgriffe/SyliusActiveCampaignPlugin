<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Repository;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

/**
 * @extends ActiveCampaignResourceRepositoryInterface<ChannelInterface>
 */
interface ActiveCampaignChannelRepositoryInterface extends ActiveCampaignResourceRepositoryInterface
{
    /** @return ChannelInterface[] */
    public function findAllForCustomer(CustomerInterface $customer): array;
}
