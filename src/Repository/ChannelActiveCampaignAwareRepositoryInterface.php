<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Repository;

use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @extends ActiveCampaignAwareRepositoryInterface<ChannelInterface>
 */
interface ChannelActiveCampaignAwareRepositoryInterface extends ActiveCampaignAwareRepositoryInterface
{
    /** @return ChannelInterface[] */
    public function findAllEnabledForActiveCampaign(): array;
}
