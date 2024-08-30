<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceCustomerMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface;

final class EcommerceCustomerUpdateHandler
{
    public function __construct(
        private EcommerceCustomerMapperInterface $ecommerceCustomerMapper,
        private ActiveCampaignResourceClientInterface $activeCampaignClient,
        private CustomerRepositoryInterface $customerRepository,
        private ChannelRepositoryInterface $channelRepository,
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

    /**
     * @throws GuzzleException
     * @throws \Throwable
     * @throws \JsonException
     */
    public function __invoke(EcommerceCustomerUpdate $message): void
    {
        $channelId = $message->getChannelId();
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->find($channelId);
        if ($channel === null) {
            throw new InvalidArgumentException(sprintf('Channel with id "%s" does not exists', $channelId));
        }
        if (!$channel instanceof ActiveCampaignAwareInterface) {
            throw new InvalidArgumentException(sprintf('The Channel entity should implement the "%s" class', ActiveCampaignAwareInterface::class));
        }

        $customerId = $message->getCustomerId();
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->find($customerId);
        if ($customer === null) {
            throw new InvalidArgumentException(sprintf('Customer with id "%s" does not exists', $customerId));
        }
        if (!$customer instanceof CustomerActiveCampaignAwareInterface) {
            throw new InvalidArgumentException(sprintf('The Customer entity should implement the "%s" class', CustomerActiveCampaignAwareInterface::class));
        }

        $channelCustomer = $customer->getChannelCustomerByChannel($channel);
        if ($channelCustomer === null) {
            throw new InvalidArgumentException(sprintf('The Customer with id "%s" does not have an ActiveCampaign Ecommerce customer for the channel "%s".', $customerId, (string) $channel->getCode()));
        }
        $activeCampaignId = $channelCustomer->getActiveCampaignId();
        if ($activeCampaignId !== $message->getActiveCampaignId()) {
            throw new InvalidArgumentException(sprintf('The Customer with id "%s" has an ActiveCampaign id that does not match. Expected "%s", given "%s".', $customerId, $message->getActiveCampaignId(), (string) $activeCampaignId));
        }

        try {
            $this->activeCampaignClient->update($message->getActiveCampaignId(), $this->ecommerceCustomerMapper->mapFromCustomerAndChannel($customer, $channel));
        } catch (\Throwable $e) {
            $this->logger?->error($e->getMessage(), $e->getTrace());

            throw $e;
        }
    }
}
