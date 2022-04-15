<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact;

use InvalidArgumentException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactListsSubscriber;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\CustomerChannelsResolverInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\ListSubscriptionStatusResolverInterface;

final class ContactListsSubscriberHandler
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private CustomerChannelsResolverInterface $customerChannelsResolver,
        private ListSubscriptionStatusResolverInterface $listSubscriptionStatusResolver
    ) {
    }

    public function __invoke(ContactListsSubscriber $message): void
    {
        $customerId = $message->getCustomerId();
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->find($customerId);
        if ($customer === null) {
            throw new InvalidArgumentException(sprintf('Customer with id "%s" does not exists.', $customerId));
        }
        if (!$customer instanceof CustomerActiveCampaignAwareInterface) {
            throw new InvalidArgumentException(sprintf('The Customer entity should implement the "%s" class.', CustomerActiveCampaignAwareInterface::class));
        }
        $activeCampaignContactId = $customer->getActiveCampaignId();
        if ($activeCampaignContactId === null) {
            throw new InvalidArgumentException(sprintf('The Customer with id "%s" does not have an ActiveCampaign id.', $customerId));
        }

        $channels = $this->customerChannelsResolver->resolve($customer);
        foreach ($channels as $channel) {
            if (!$channel instanceof ChannelActiveCampaignAwareInterface) {
                continue;
            }
            $this->listSubscriptionStatusResolver->resolve($customer, $channel);
        }
    }
}
