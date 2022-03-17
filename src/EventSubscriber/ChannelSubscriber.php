<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\EventSubscriber;

use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

final class ChannelSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.channel.post_create' => ['createChannel'],
            'sylius.channel.post_update' => ['updateChannel'],
            'sylius.channel.post_delete' => ['removeChannel'],
        ];
    }

    public function createChannel(GenericEvent $event): void
    {
        $channel = $event->getSubject();
        if (!$channel instanceof ChannelInterface) {
            return;
        }
        /** @var mixed $channelId */
        $channelId = $channel->getId();
        if ($channelId === null) {
            return;
        }
        if (!is_int($channelId)) {
            $channelId = (string) $channelId;
        }

        $this->messageBus->dispatch(new ConnectionCreate($channelId));
    }

    public function updateChannel(GenericEvent $event): void
    {
        $channel = $event->getSubject();
        if (!$channel instanceof ChannelInterface || !$channel instanceof ActiveCampaignAwareInterface) {
            return;
        }
        /** @var mixed $channelId */
        $channelId = $channel->getId();
        if ($channelId === null) {
            return;
        }
        if (!is_int($channelId)) {
            $channelId = (string) $channelId;
        }
        $activeCampaignId = $channel->getActiveCampaignId();
        if ($activeCampaignId === null) {
            return;
        }

        $this->messageBus->dispatch(new ConnectionUpdate($channelId, $activeCampaignId));
    }

    public function removeChannel(GenericEvent $event): void
    {
        $channel = $event->getSubject();
        if (!$channel instanceof ActiveCampaignAwareInterface) {
            return;
        }
        $activeCampaignId = $channel->getActiveCampaignId();
        if ($activeCampaignId === null) {
            return;
        }

        $this->messageBus->dispatch(new ConnectionRemove($activeCampaignId));
    }
}
