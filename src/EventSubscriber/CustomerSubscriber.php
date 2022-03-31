<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\EventSubscriber;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\ContactEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\EcommerceCustomerEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactRemove;
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
        private EcommerceCustomerEnqueuerInterface $ecommerceCustomerEnqueuer
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.customer.post_register' => [['enqueueContact'], ['enqueueEcommerceCustomer']],
            'sylius.customer.post_create' => [['enqueueContact'], ['enqueueEcommerceCustomer']],
            'sylius.customer.post_update' => [['enqueueContact'], ['enqueueEcommerceCustomer']],
            'sylius.customer.post_delete' => [['removeContact'], ['removeEcommerceCustomer']],
        ];
    }

    public function enqueueContact(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        if (!$customer instanceof CustomerInterface || !$customer instanceof CustomerActiveCampaignAwareInterface) {
            return;
        }
        $this->contactEnqueuer->enqueue($customer);
    }

    public function enqueueEcommerceCustomer(GenericEvent $event): void
    {
        $customer = $event->getSubject();
        if (!$customer instanceof CustomerInterface || !$customer instanceof CustomerActiveCampaignAwareInterface) {
            return;
        }
        foreach ($this->customerChannelsResolver->resolve($customer) as $channel) {
            if (!$channel instanceof ActiveCampaignAwareInterface) {
                return;
            }
            $this->ecommerceCustomerEnqueuer->enqueue($customer, $channel);
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
