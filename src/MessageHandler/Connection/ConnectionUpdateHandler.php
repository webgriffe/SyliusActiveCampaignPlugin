<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Connection;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ConnectionMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

final class ConnectionUpdateHandler
{
    public function __construct(
        private ConnectionMapperInterface $connectionMapper,
        private ActiveCampaignResourceClientInterface $activeCampaignConnectionClient,
        private ChannelRepositoryInterface $channelRepository,
        private ?LoggerInterface $logger = null,
    ) {
        if ($this->logger === null) {
            trigger_deprecation(
                'webgriffe/sylius-active-campaign-plugin',
                'v0.12.2',
                'The logger argument is mandatory.',
            );
        }
    }

    /**
     * @throws \Throwable
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function __invoke(ConnectionUpdate $message): void
    {
        $channelId = $message->getChannelId();
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->find($channelId);
        if ($channel === null) {
            throw new InvalidArgumentException(sprintf('Channel with id "%s" does not exists.', $channelId));
        }
        if (!$channel instanceof ActiveCampaignAwareInterface) {
            throw new InvalidArgumentException(sprintf('The Channel entity should implement the "%s" class.', ActiveCampaignAwareInterface::class));
        }

        $activeCampaignId = $channel->getActiveCampaignId();
        if ($activeCampaignId !== $message->getActiveCampaignId()) {
            throw new InvalidArgumentException(sprintf('The Channel with id "%s" has an ActiveCampaign id that does not match. Expected "%s", given "%s".', $channelId, $message->getActiveCampaignId(), (string) $activeCampaignId));
        }

        try {
            $this->activeCampaignConnectionClient->update($message->getActiveCampaignId(), $this->connectionMapper->mapFromChannel($channel));
        } catch (\Throwable $e) {
            $this->logger?->error($e->getMessage(), $e->getTrace());

            throw $e;
        }
    }
}
