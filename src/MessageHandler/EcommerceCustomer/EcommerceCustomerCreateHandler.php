<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer;

use InvalidArgumentException;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceCustomerMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer\CreateEcommerceCustomerResponse;
use Webmozart\Assert\Assert;

final class EcommerceCustomerCreateHandler
{
    public function __construct(
        private EcommerceCustomerMapperInterface $ecommerceCustomerMapper,
        private ActiveCampaignResourceClientInterface $activeCampaignClient,
        private CustomerRepositoryInterface $customerRepository,
        private ChannelRepositoryInterface $channelRepository
    ) {
    }

    public function __invoke(EcommerceCustomerCreate $message): void
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
        if (!$customer instanceof ActiveCampaignAwareInterface) {
            throw new InvalidArgumentException(sprintf('The Customer entity should implement the "%s" class', ActiveCampaignAwareInterface::class));
        }

        $activeCampaignId = $customer->getActiveCampaignId();
        if ($activeCampaignId !== null) {
            throw new InvalidArgumentException(sprintf('The Customer with id "%s" has been already created on ActiveCampaign on the EcommerceCustomer with id "%s"', $customerId, $activeCampaignId));
        }

        /** @var CreateResourceResponseInterface|CreateEcommerceCustomerResponse $response */
        $response = $this->activeCampaignClient->create($this->ecommerceCustomerMapper->mapFromCustomerAndChannel($customer, $channel));
        Assert::isInstanceOf($response, CreateEcommerceCustomerResponse::class);
        $customer->setActiveCampaignId($response->getEcomCustomer()->getId());
        $this->customerRepository->add($customer);
    }
}
