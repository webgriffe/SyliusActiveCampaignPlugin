<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\ListSubscriptionStatusResolverExceptionInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactListMapperInterface;
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
        private ListSubscriptionStatusResolverInterface $listSubscriptionStatusResolver,
        private ActiveCampaignResourceClientInterface $activeCampaignContactListClient,
        private ContactListMapperInterface $contactListMapper,
        private LoggerInterface $logger
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
            $activeCampaignListId = $channel->getActiveCampaignListId();
            if ($activeCampaignListId === null) {
                continue;
            }

            try {
                $listSubscriptionStatus = $this->listSubscriptionStatusResolver->resolve($customer, $channel);
            } catch (ListSubscriptionStatusResolverExceptionInterface $exception) {
                $this->logger->info(sprintf('Unable to resolve for the customer "%s" the subscription status for the list "%s" of channel "%s".', (string) $customer->getEmail(), $activeCampaignListId, (string) $channel->getCode()));

                continue;
            }

            try {
                $this->activeCampaignContactListClient->create($this->contactListMapper->mapFromListContactStatusAndSourceId(
                    $activeCampaignListId,
                    $activeCampaignContactId,
                    $listSubscriptionStatus
                ));
            } catch (HttpException $httpException) {
                if ($httpException->getStatusCode() !== 200) {
                    throw $httpException;
                }
                $this->logger->info(sprintf('The association with the list with id "%s" already exists for the contact with id "%s".', $activeCampaignListId, $activeCampaignContactId));

                continue;
            }
        }
    }
}
