<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use Doctrine\ORM\EntityManagerInterface;
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
        private EntityManagerInterface $entityManager
    ) {
    }

    public function enqueue($channel): void
    {
        /** @var string|int|null $channelId */
        $channelId = $channel->getId();
        Assert::notNull($channelId, 'The channel id should not be null');
        $activeCampaignConnectionId = $channel->getActiveCampaignId();
        if ($activeCampaignConnectionId !== null) {
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

            $this->messageBus->dispatch(new ConnectionUpdate($channelId, $activeCampaignConnectionId));

            return;
        }

        $this->messageBus->dispatch(new ConnectionCreate($channelId));
    }
}
