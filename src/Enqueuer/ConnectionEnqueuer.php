<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection\ConnectionResponse;
use Webmozart\Assert\Assert;

final class ConnectionEnqueuer implements ConnectionEnqueuerInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private ActiveCampaignResourceClientInterface $activeCampaignConnectionClient,
        private EntityManagerInterface $entityManager,
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

    public function enqueue($channel): void
    {
        /** @var string|int|null $channelId */
        $channelId = $channel->getId();
        Assert::notNull($channelId, 'The channel id should not be null');
        $this->logger?->debug(sprintf(
            'Starting enqueuing connection for channel "%s".',
            $channelId,
        ));
        $activeCampaignConnectionId = $channel->getActiveCampaignId();
        if ($activeCampaignConnectionId !== null) {
            $this->logger?->debug(sprintf(
                'Channel "%s" has an already valued ActiveCampaign id "%s", so we have to update the connection.',
                $channelId,
                $activeCampaignConnectionId,
            ));
            $this->messageBus->dispatch(new ConnectionUpdate($channelId, $activeCampaignConnectionId));

            return;
        }
        $code = $channel->getCode();
        Assert::notNull($code, 'The channel code should not be null');
        $searchConnectionsForEmail = $this->activeCampaignConnectionClient->list([
            'filters[service]' => 'sylius',
            'filters[externalid]' => $code,
        ])->getResourceResponseLists();
        if (count($searchConnectionsForEmail) > 0) {
            /** @var ConnectionResponse $connection */
            $connection = reset($searchConnectionsForEmail);
            $activeCampaignConnectionId = $connection->getId();
            $channel->setActiveCampaignId($activeCampaignConnectionId);
            $this->entityManager->flush();
            $this->logger?->debug(sprintf(
                'Found an ActiveCampaign connection with id "%s" for given channel "%s", the id has been stored and we have to update the connection.',
                $activeCampaignConnectionId,
                $channelId,
            ));

            $this->messageBus->dispatch(new ConnectionUpdate($channelId, $activeCampaignConnectionId));

            return;
        }
        $this->logger?->debug(sprintf(
            'No connection found for given channel "%s", we have to create the connection.',
            $channelId,
        ));

        $this->messageBus->dispatch(new ConnectionCreate($channelId));
    }
}
