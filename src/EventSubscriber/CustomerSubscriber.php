<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\EventSubscriber;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\ContactCreate;

final class CustomerSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.customer.post_create' => ['createContact'],
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
}
