<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer\EcommerceCustomerResponse;
use Webmozart\Assert\Assert;

final class EcommerceCustomerEnqueuer implements EcommerceCustomerEnqueuerInterface
{
    /**
     * @param FactoryInterface<ChannelCustomerInterface> $channelCustomerFactory
     */
    public function __construct(
        private MessageBusInterface $messageBus,
        private ActiveCampaignResourceClientInterface $activeCampaignEcommerceCustomerClient,
        private EntityManagerInterface $entityManager,
        private FactoryInterface $channelCustomerFactory,
        private ?LoggerInterface $logger = null,
    ) {
        if ($this->logger === null) {
            trigger_deprecation(
                'webgriffe/sylius-active-campaign-plugin',
                'v0.12.2',
                'The logger argument is mandatory.',
            );
        }
    }

    public function enqueue($customer, $channel): void
    {
        /** @var string|int|null $customerId */
        $customerId = $customer->getId();
        Assert::notNull($customerId, 'The customer id should not be null');
        /** @var string|int|null $channelId */
        $channelId = $channel->getId();
        Assert::notNull($channelId, 'The channel id should not be null');
        $this->logger?->debug(sprintf(
            'Starting enqueuing ecommerce customer for customer "%s" and channel "%s".',
            $customerId,
            $channelId,
        ));

        $channelCustomer = $customer->getChannelCustomerByChannel($channel);
        if ($channelCustomer !== null) {
            $this->logger?->debug(sprintf(
                'Customer "%s" has an already valued ActiveCampaign id "%s" for channel "%s", so we have to update the ecommerce customer.',
                $customerId,
                $channelCustomer->getActiveCampaignId(),
                $channelId,
            ));
            $this->messageBus->dispatch(new EcommerceCustomerUpdate($customerId, $channelCustomer->getActiveCampaignId(), $channelId));

            return;
        }

        $email = $customer->getEmail();
        Assert::notNull($email, 'The customer email should not be null');
        $activeCampaignChannelId = $channel->getActiveCampaignId();
        Assert::notNull($activeCampaignChannelId, sprintf('You should export the channel "%s" to Active Campaign before enqueuing the customer "%s"', $channelId, $email));

        $ecommerceCustomerList = $this->activeCampaignEcommerceCustomerClient->list([
            'filters[email]' => $email,
            'filters[connectionid]' => (string) $activeCampaignChannelId,
        ])->getResourceResponseLists();
        if (count($ecommerceCustomerList) > 0) {
            /** @var EcommerceCustomerResponse $ecommerceCustomer */
            $ecommerceCustomer = reset($ecommerceCustomerList);
            $activeCampaignEcommerceCustomerId = $ecommerceCustomer->getId();
            $channelCustomer = $this->channelCustomerFactory->createNew();
            $channelCustomer->setActiveCampaignId($activeCampaignEcommerceCustomerId);
            $channelCustomer->setChannel($channel);
            $channelCustomer->setCustomer($customer);
            $this->entityManager->persist($channelCustomer);
            $customer->addChannelCustomer($channelCustomer);
            $this->entityManager->flush();
            $this->logger?->debug(sprintf(
                'Found an ActiveCampaign ecommerce customer with id "%s" for given customer "%s" and channel "%s", the id has been stored and we have to update the ecommerce customer.',
                $activeCampaignEcommerceCustomerId,
                $customerId,
                $channelId,
            ));

            $this->messageBus->dispatch(new EcommerceCustomerUpdate($customerId, $activeCampaignEcommerceCustomerId, $channelId));

            return;
        }
        $this->logger?->debug(sprintf(
            'No ecommerce customer found for given customer "%s" and channel "%s", we have to create the ecommerce customer.',
            $customerId,
            $channelId,
        ));

        $this->messageBus->dispatch(new EcommerceCustomerCreate($customerId, $channelId));
    }
}
