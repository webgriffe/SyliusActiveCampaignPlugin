<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\EventSubscriber;

use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\ConnectionEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Connection\ConnectionRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

final class ChannelSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private ConnectionEnqueuerInterface $connectionEnqueuer
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.channel.post_create' => ['enqueueChannel'],
            'sylius.channel.post_update' => ['enqueueChannel'],
            'sylius.channel.post_delete' => ['removeChannel'],
        ];
    }

    public function enqueueChannel(GenericEvent $event): void
    {
        $channel = $event->getSubject();
        if (!$channel instanceof ChannelInterface || !$channel instanceof ActiveCampaignAwareInterface) {
            return;
        }
        $this->connectionEnqueuer->enqueue($channel);
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
