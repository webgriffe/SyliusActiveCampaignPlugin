<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\EventSubscriber;

use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\ContactEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\EcommerceCustomerEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactListsSubscriber;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactTagsAdder;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerRemove;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\CustomerChannelsResolverInterface;

final class CustomerSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private CustomerChannelsResolverInterface $customerChannelsResolver,
        private ContactEnqueuerInterface $contactEnqueuer,
        private EcommerceCustomerEnqueuerInterface $ecommerceCustomerEnqueuer,
        private LoggerInterface $logger,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.customer.post_register' => [['enqueueContact'], ['enqueueEcommerceCustomer'], ['addContactTags'], ['subscribeContactToLists']],
            'sylius.customer.post_create' => [['enqueueContact'], ['enqueueEcommerceCustomer'], ['addContactTags'], ['subscribeContactToLists']],
            'sylius.customer.post_update' => [['enqueueContact'], ['enqueueEcommerceCustomer'], ['addContactTags'], ['subscribeContactToLists']],
            'sylius.customer.post_delete' => [['removeContact'], ['removeEcommerceCustomer']],
        ];
    }

    public function enqueueContact(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        if (!$customer instanceof CustomerInterface || !$customer instanceof CustomerActiveCampaignAwareInterface) {
            return;
        }
        $this->logger->debug(sprintf(
            'Invoked contact enqueuing for customer "%s".',
            (string) $customer->getId(),
        ));

        try {
            $this->contactEnqueuer->enqueue($customer);
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage(), $throwable->getTrace());
        }
    }

    public function enqueueEcommerceCustomer(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        if (!$customer instanceof CustomerInterface || !$customer instanceof CustomerActiveCampaignAwareInterface) {
            return;
        }
        $this->logger->debug(sprintf(
            'Invoked ecommerce customer enqueuing for customer "%s".',
            (string) $customer->getId(),
        ));
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
        $customer = $event->getSubject();
        if (!$customer instanceof CustomerInterface || !$customer instanceof CustomerActiveCampaignAwareInterface) {
            return;
        }
        /** @var int|string|null $customerId */
        $customerId = $customer->getId();
        if ($customerId === null) {
            return;
        }
        $this->logger->debug(sprintf(
            'Invoked adding contact tags for customer "%s".',
            $customerId,
        ));

        try {
            $this->messageBus->dispatch(new ContactTagsAdder($customerId));
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage(), $throwable->getTrace());
        }
    }

    public function subscribeContactToLists(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        if (!$customer instanceof CustomerInterface || !$customer instanceof CustomerActiveCampaignAwareInterface) {
            return;
        }
        /** @var int|string|null $customerId */
        $customerId = $customer->getId();
        if ($customerId === null) {
            return;
        }
        $this->logger->debug(sprintf(
            'Invoked subscribing contact to lists for customer "%s".',
            $customerId,
        ));

        try {
            $this->messageBus->dispatch(new ContactListsSubscriber($customerId));
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage(), $throwable->getTrace());
        }
    }

    public function removeContact(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        if (!$customer instanceof CustomerInterface || !$customer instanceof ActiveCampaignAwareInterface) {
            return;
        }
        $this->logger->debug(sprintf(
            'Invoked remove contact for customer "%s".',
            (string) $customer->getId(),
        ));
        $activeCampaignId = $customer->getActiveCampaignId();
        if ($activeCampaignId === null) {
            return;
        }

        try {
            $this->messageBus->dispatch(new ContactRemove($activeCampaignId));
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage(), $throwable->getTrace());
        }
    }

    public function removeEcommerceCustomer(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        if (!$customer instanceof CustomerInterface || !$customer instanceof CustomerActiveCampaignAwareInterface) {
            return;
        }
        $this->logger->debug(sprintf(
            'Invoked remove ecommerce customer for customer "%s".',
            (string) $customer->getId(),
        ));
        $activeCampaignId = $customer->getActiveCampaignId();
        if ($activeCampaignId === null) {
            return;
        }
        foreach ($customer->getChannelCustomers() as $channelCustomer) {
            try {
                $this->messageBus->dispatch(new EcommerceCustomerRemove($channelCustomer->getActiveCampaignId()));
            } catch (Throwable $throwable) {
                $this->logger->error($throwable->getMessage(), $throwable->getTrace());
            }
        }
    }
}
