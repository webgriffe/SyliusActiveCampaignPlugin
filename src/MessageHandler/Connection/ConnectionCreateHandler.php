<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Connection;

use InvalidArgumentException;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ConnectionMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

final class ConnectionCreateHandler
{
    public function __construct(
        private ConnectionMapperInterface $connectionMapper,
        private ActiveCampaignResourceClientInterface $activeCampaignConnectionClient,
        private ChannelRepositoryInterface $channelRepository,
    ) {
    }

    public function __invoke(ConnectionCreate $message): void
    {
        $channelId = $message->getChannelId();
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->find($channelId);
        if ($channel === null) {
            throw new InvalidArgumentException(sprintf('Channel with id "%s" does not exists', $channelId));
        }
        if (!$channel instanceof ActiveCampaignAwareInterface) {
            throw new InvalidArgumentException(sprintf('The Channel entity should implement the "%s" class', ActiveCampaignAwareInterface::class));
        }

        $activeCampaignId = $channel->getActiveCampaignId();
        if ($activeCampaignId !== null) {
            throw new InvalidArgumentException(sprintf('The Channel with id "%s" has been already created on ActiveCampaign on the connection with id "%s"', $channelId, $activeCampaignId));
        }
        $createConnectionResponse = $this->activeCampaignConnectionClient->create($this->connectionMapper->mapFromChannel($channel));
        $channel->setActiveCampaignId($createConnectionResponse->getResourceResponse()->getId());
        $this->channelRepository->add($channel);
    }
}
