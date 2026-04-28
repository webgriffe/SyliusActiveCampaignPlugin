<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Connection;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ConnectionMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection\ConnectionResponse;

final class ConnectionCreateHandler
{
    public function __construct(
        private ConnectionMapperInterface $connectionMapper,
        private ActiveCampaignResourceClientInterface $activeCampaignConnectionClient,
        private ChannelRepositoryInterface $channelRepository,
        private ?LoggerInterface $logger = null,
        private ?MessageBusInterface $messageBus = null,
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
     * @throws GuzzleException
     * @throws \Throwable
     * @throws \JsonException
     */
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
            $this->logger?->warning(sprintf(
                'The Channel with id "%s" has been already created on ActiveCampaign on the connection with id "%s". Skipping creation.',
                $channelId,
                $activeCampaignId,
            ));

            return;
        }

        try {
            $activeCampaignConnectionId = $this->activeCampaignConnectionClient->create($this->connectionMapper->mapFromChannel($channel))->getResourceResponse()->getId();
            $linkedExistingConnection = false;
        } catch (UnprocessableEntityHttpException $e) {
            $searchConnections = $this->activeCampaignConnectionClient->list([
                'filters[service]' => 'sylius',
                'filters[externalid]' => (string) $channel->getCode(),
            ])->getResourceResponseLists();
            if (count($searchConnections) < 1) {
                throw $e;
            }
            /** @var ConnectionResponse $existingConnection */
            $existingConnection = reset($searchConnections);
            $activeCampaignConnectionId = $existingConnection->getId();
            $linkedExistingConnection = true;
            $this->logger?->warning(sprintf(
                'Connection for channel with code "%s" already exists on ActiveCampaign with id "%s". Why it has not been found before?',
                (string) $channel->getCode(),
                $activeCampaignConnectionId,
            ));
        } catch (\Throwable $e) {
            $this->logger?->error($e->getMessage(), $e->getTrace());

            throw $e;
        }
        $channel->setActiveCampaignId($activeCampaignConnectionId);
        $this->channelRepository->add($channel);
        if ($linkedExistingConnection) {
            $this->messageBus?->dispatch(new ConnectionUpdate($message->getChannelId(), $activeCampaignConnectionId));
        }
    }
}
