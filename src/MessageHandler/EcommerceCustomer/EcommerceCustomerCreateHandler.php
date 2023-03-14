<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceCustomerMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface;

final class EcommerceCustomerCreateHandler
{
    /**
     * @param FactoryInterface<ChannelCustomerInterface> $channelCustomerFactory
     */
    public function __construct(
        private EcommerceCustomerMapperInterface $ecommerceCustomerMapper,
        private ActiveCampaignResourceClientInterface $activeCampaignClient,
        private CustomerRepositoryInterface $customerRepository,
        private ChannelRepositoryInterface $channelRepository,
        private FactoryInterface $channelCustomerFactory,
        private EntityManagerInterface $entityManager,
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
        if (!$customer instanceof CustomerActiveCampaignAwareInterface) {
            throw new InvalidArgumentException(sprintf('The Customer entity should implement the "%s" class', CustomerActiveCampaignAwareInterface::class));
        }

        $channelCustomer = $customer->getChannelCustomerByChannel($channel);
        if ($channelCustomer !== null) {
            $activeCampaignId = $channelCustomer->getActiveCampaignId();

            throw new InvalidArgumentException(sprintf('The Customer with id "%s" has been already created on ActiveCampaign on the EcommerceCustomer with id "%s"', $customerId, $activeCampaignId));
        }
        $response = $this->activeCampaignClient->create($this->ecommerceCustomerMapper->mapFromCustomerAndChannel($customer, $channel));
        $channelCustomer = $this->channelCustomerFactory->createNew();
        $channelCustomer->setCustomer($customer);
        $channelCustomer->setActiveCampaignId($response->getResourceResponse()->getId());
        $channelCustomer->setChannel($channel);
        $this->entityManager->persist($channelCustomer);
        $customer->addChannelCustomer($channelCustomer);
        $this->customerRepository->add($customer);
    }
}
