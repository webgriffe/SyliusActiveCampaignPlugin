<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact;

use InvalidArgumentException;
use LogicException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactListsUpdater;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\CustomerChannelsResolverInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\ListSubscriptionStatusResolverInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Updater\ListSubscriptionStatusUpdaterInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\RetrieveContactResponseInterface;

final class ContactListsUpdaterHandler
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private CustomerChannelsResolverInterface $customerChannelsResolver,
        private ActiveCampaignResourceClientInterface $activeCampaignContactClient,
        private ListSubscriptionStatusUpdaterInterface $listSubscriptionUpdater
    ) {
    }

    public function __invoke(ContactListsUpdater $message): void
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

        $retrieveContact = $this->activeCampaignContactClient->get($activeCampaignContactId);
        if (!$retrieveContact instanceof RetrieveContactResponseInterface) {
            throw new LogicException(sprintf('The retrieve contact response for the contact with id "%s" from customer with id "%s" is not valid.', $activeCampaignContactId, $customerId));
        }
        if ($retrieveContact->getResourceResponse()->getId() !== $activeCampaignContactId) {
            throw new LogicException(sprintf('The retrieved contact has id "%s" that does not match with the contact id "%s" used for search it.', $retrieveContact->getResourceResponse()->getId(), $activeCampaignContactId));
        }
        $contactListsSubscription = [];
        foreach ($retrieveContact->getContactLists() as $contactList) {
            $contactListsSubscription[(int) $contactList['list']] = (int) $contactList['status'];
        }

        $channels = $this->customerChannelsResolver->resolve($customer);
        foreach ($channels as $channel) {
            if (!$channel instanceof ChannelActiveCampaignAwareInterface) {
                continue;
            }
            $activeCampaignListId = $channel->getActiveCampaignListId();
            if ($activeCampaignListId === null) {
                continue;
            }
            if (!array_key_exists($activeCampaignListId, $contactListsSubscription)) {
                continue;
            }

            $this->listSubscriptionUpdater->update($customer, $channel, $this->getActiveCampaignSubscriptionStatusCode($contactListsSubscription[$activeCampaignListId]));
        }
    }

    private function getActiveCampaignSubscriptionStatusCode(int $status): int
    {
        if (in_array($status, [
            ListSubscriptionStatusResolverInterface::UNCONFIRMED_STATUS_CODE,
            ListSubscriptionStatusResolverInterface::SUBSCRIBED_STATUS_CODE,
            ListSubscriptionStatusResolverInterface::UNSUBSCRIBED_STATUS_CODE,
            ListSubscriptionStatusResolverInterface::BOUNCED_STATUS_CODE,
            ], true)) {
            return $status;
        }

        throw new InvalidArgumentException(sprintf('The status code "%s" is not recognized.', $status));
    }
}
