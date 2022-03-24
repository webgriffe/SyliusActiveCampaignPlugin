<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\EventSubscriber;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface;
use Webmozart\Assert\Assert;

final class CustomerSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private ChannelRepositoryInterface $channelRepository,
        private RepositoryInterface $channelCustomerRepository
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.customer.post_create' => [['createContact'], ['createEcommerceCustomer']],
            'sylius.customer.post_update' => [['updateContact'], ['updateEcommerceCustomer']],
            'sylius.customer.post_delete' => [['removeContact'], ['removeEcommerceCustomer']],
        ];
    }

    public function createContact(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        if (!$customer instanceof CustomerInterface) {
            return;
        }
        /** @var mixed $customerId */
        $customerId = $customer->getId();
        if ($customerId === null) {
            return;
        }
        if (!is_int($customerId)) {
            $customerId = (string) $customerId;
        }

        $this->messageBus->dispatch(new ContactCreate($customerId));
    }

    public function createEcommerceCustomer(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        if (!$customer instanceof CustomerInterface) {
            return;
        }
        /** @var mixed $customerId */
        $customerId = $customer->getId();
        if ($customerId === null) {
            return;
        }
        if (!is_int($customerId)) {
            $customerId = (string) $customerId;
        }
        /** @var ChannelInterface $channel */
        foreach ($this->channelRepository->findBy(['enabled' => true]) as $channel) {
            /** @var mixed $channelId */
            $channelId = $channel->getId();
            if ($channelId === null) {
                continue;
            }
            if (!is_int($channelId)) {
                $channelId = (string) $channelId;
            }
            $this->messageBus->dispatch(new EcommerceCustomerCreate($customerId, $channelId));
        }
    }

    public function updateContact(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        if (!$customer instanceof CustomerInterface || !$customer instanceof ActiveCampaignAwareInterface) {
            return;
        }
        /** @var mixed $customerId */
        $customerId = $customer->getId();
        if ($customerId === null) {
            return;
        }
        if (!is_int($customerId)) {
            $customerId = (string) $customerId;
        }
        $activeCampaignId = $customer->getActiveCampaignId();
        if ($activeCampaignId === null) {
            return;
        }

        $this->messageBus->dispatch(new ContactUpdate($customerId, $activeCampaignId));
    }

    public function updateEcommerceCustomer(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        if (!$customer instanceof CustomerInterface || !$customer instanceof CustomerActiveCampaignAwareInterface) {
            return;
        }
        /** @var mixed $customerId */
        $customerId = $customer->getId();
        if ($customerId === null) {
            return;
        }
        if (!is_int($customerId)) {
            $customerId = (string) $customerId;
        }
        $activeCampaignId = $customer->getActiveCampaignId();
        if ($activeCampaignId === null) {
            return;
        }
        foreach ($customer->getChannelCustomers() as $channelCustomer) {
            /** @var int|string|null $channelId */
            $channelId = $channelCustomer->getChannel()->getId();
            Assert::notNull($channelId);
            $this->messageBus->dispatch(new EcommerceCustomerUpdate($customerId, $channelCustomer->getActiveCampaignId(), $channelId));
        }
    }

    public function removeContact(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        if (!$customer instanceof ActiveCampaignAwareInterface) {
            return;
        }
        $activeCampaignId = $customer->getActiveCampaignId();
        if ($activeCampaignId === null) {
            return;
        }

        $this->messageBus->dispatch(new ContactRemove($activeCampaignId));
    }

    public function removeEcommerceCustomer(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        if (!$customer instanceof CustomerActiveCampaignAwareInterface) {
            return;
        }
        $activeCampaignId = $customer->getActiveCampaignId();
        if ($activeCampaignId === null) {
            return;
        }
        foreach ($customer->getChannelCustomers() as $channelCustomer) {
            $this->messageBus->dispatch(new EcommerceCustomerRemove($channelCustomer->getActiveCampaignId()));
        }
    }
}
