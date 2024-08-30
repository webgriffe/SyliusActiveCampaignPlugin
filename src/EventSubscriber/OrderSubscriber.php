<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\EventSubscriber;

use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\ContactEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\EcommerceCustomerEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\EcommerceOrderEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactListsSubscriber;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactTagsAdder;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\CustomerChannelsResolverInterface;

final class OrderSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private EcommerceOrderEnqueuerInterface $ecommerceOrderEnqueuer,
        private LoggerInterface $logger,
        private ContactEnqueuerInterface $contactEnqueuer,
        private EcommerceCustomerEnqueuerInterface $ecommerceCustomerEnqueuer,
        private CustomerChannelsResolverInterface $customerChannelsResolver,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.order.post_complete' => [['enqueueContact'], ['enqueueEcommerceCustomer'], ['addContactTags'], ['subscribeContactToLists'], ['enqueueOrderInRealTime']],
            'sylius.order.post_create' => [['enqueueContact'], ['enqueueEcommerceCustomer'], ['addContactTags'], ['subscribeContactToLists'], ['enqueueOrderNotInRealTime']],
            'sylius.order.post_update' => [['enqueueContact'], ['enqueueEcommerceCustomer'], ['addContactTags'], ['subscribeContactToLists'], ['enqueueOrderNotInRealTime']],
            'sylius.order.post_delete' => ['removeOrder'],
        ];
    }

    public function enqueueContact(GenericEvent $event): void
    {
        $order = $event->getSubject();
        if (!$order instanceof OrderInterface || !$order instanceof ActiveCampaignAwareInterface || null === $customer = $order->getCustomer()) {
            return;
        }
        $this->logger->debug(sprintf(
            'Invoked contact enqueuing for customer "%s" by order "%s".',
            (string) $customer->getId(),
            (string) $order->getId(),
        ));
        if (!$customer instanceof CustomerInterface || !$customer instanceof CustomerActiveCampaignAwareInterface) {
            return;
        }

        try {
            $this->contactEnqueuer->enqueue($customer);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage(), $throwable->getTrace());
        }
    }

    public function enqueueEcommerceCustomer(GenericEvent $event): void
    {
        $order = $event->getSubject();
        if (!$order instanceof OrderInterface || !$order instanceof ActiveCampaignAwareInterface || null === $customer = $order->getCustomer()) {
            return;
        }
        $this->logger->debug(sprintf(
            'Invoked ecommerce customer enqueuing for customer "%s" by order "%s".',
            (string) $customer->getId(),
            (string) $order->getId(),
        ));
        if (!$customer instanceof CustomerInterface || !$customer instanceof CustomerActiveCampaignAwareInterface) {
            return;
        }
        foreach ($this->customerChannelsResolver->resolve($customer) as $channel) {
            if (!$channel instanceof ActiveCampaignAwareInterface) {
                return;
            }

            try {
                $this->ecommerceCustomerEnqueuer->enqueue($customer, $channel);
            } catch (Throwable $throwable) {
                $this->logger->error($throwable->getMessage(), $throwable->getTrace());
            }
        }
    }

    public function addContactTags(GenericEvent $event): void
    {
        $order = $event->getSubject();
        if (!$order instanceof OrderInterface || !$order instanceof ActiveCampaignAwareInterface || $order->getCustomer() === null) {
            return;
        }
        $customer = $order->getCustomer();
        if (!$customer instanceof CustomerInterface || !$customer instanceof CustomerActiveCampaignAwareInterface) {
            return;
        }
        $this->logger->debug(sprintf(
            'Invoked adding contact tags for customer "%s" by order "%s".',
            (string) $customer->getId(),
            (string) $order->getId(),
        ));
        /** @var int|string|null $customerId */
        $customerId = $customer->getId();
        if ($customerId === null) {
            return;
        }

        try {
            $this->messageBus->dispatch(new ContactTagsAdder($customerId));
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage(), $throwable->getTrace());
        }
    }

    public function subscribeContactToLists(GenericEvent $event): void
    {
        $order = $event->getSubject();
        if (!$order instanceof OrderInterface || !$order instanceof ActiveCampaignAwareInterface || $order->getCustomer() === null) {
            return;
        }
        $customer = $order->getCustomer();
        if (!$customer instanceof CustomerInterface || !$customer instanceof CustomerActiveCampaignAwareInterface) {
            return;
        }
        $this->logger->debug(sprintf(
            'Invoked subscribing contact to lists for customer "%s" by order "%s".',
            (string) $customer->getId(),
            (string) $order->getId(),
        ));
        /** @var int|string|null $customerId */
        $customerId = $customer->getId();
        if ($customerId === null) {
            return;
        }

        try {
            $this->messageBus->dispatch(new ContactListsSubscriber($customerId));
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage(), $throwable->getTrace());
        }
    }

    public function enqueueOrderInRealTime(GenericEvent $event): void
    {
        $this->enqueueOrder($event, true);
    }

    public function enqueueOrderNotInRealTime(GenericEvent $event): void
    {
        $this->enqueueOrder($event, false);
    }

    private function enqueueOrder(GenericEvent $event, bool $isInRealTime): void
    {
        $order = $event->getSubject();
        if (!$order instanceof OrderInterface || !$order instanceof ActiveCampaignAwareInterface || $order->getCustomer() === null) {
            return;
        }
        $this->logger->debug(sprintf(
            'Invoked ecommerce order enqueuing for order "%s".',
            (string) $order->getId(),
        ), ['is_in_real_time' => $isInRealTime]);

        try {
            $this->ecommerceOrderEnqueuer->enqueue($order, $isInRealTime);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage(), $throwable->getTrace());
        }
    }

    public function removeOrder(GenericEvent $event): void
    {
        $order = $event->getSubject();
        if (!$order instanceof OrderInterface || !$order instanceof ActiveCampaignAwareInterface) {
            return;
        }
        $activeCampaignId = $order->getActiveCampaignId();
        if ($activeCampaignId === null) {
            return;
        }
        $this->logger->debug(sprintf(
            'Invoked remove ecommerce order for order "%s".',
            (string) $order->getId(),
        ));

        try {
            $this->messageBus->dispatch(new EcommerceOrderRemove($activeCampaignId));
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage(), $throwable->getTrace());
        }
    }
}
