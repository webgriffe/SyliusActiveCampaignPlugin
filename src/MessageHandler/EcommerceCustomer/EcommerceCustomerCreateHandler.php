<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer;

if (!interface_exists(\Sylius\Resource\Factory\FactoryInterface::class)) {
    class_alias(\Sylius\Component\Resource\Factory\FactoryInterface::class, \Sylius\Resource\Factory\FactoryInterface::class);
}
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceCustomerMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer\EcommerceCustomerResponse;

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
            $this->logger?->warning(sprintf(
                'The Customer with id "%s" has been already created on ActiveCampaign on the EcommerceCustomer with id "%s". Skipping creation.',
                $customerId,
                $activeCampaignId,
            ));

            return;
        }

        try {
            $response = $this->activeCampaignClient->create($this->ecommerceCustomerMapper->mapFromCustomerAndChannel($customer, $channel));
            $activeCampaignEcommerceCustomerId = $response->getResourceResponse()->getId();
            $linkedExistingEcommerceCustomer = false;
        } catch (UnprocessableEntityHttpException $e) {
            $searchEcommerceCustomers = $this->activeCampaignClient->list([
                'filters[email]' => (string) $customer->getEmail(),
                'filters[connectionid]' => (string) $channel->getActiveCampaignId(),
            ])->getResourceResponseLists();
            if (count($searchEcommerceCustomers) < 1) {
                throw $e;
            }
            /** @var EcommerceCustomerResponse $ecommerceCustomer */
            $ecommerceCustomer = reset($searchEcommerceCustomers);
            $activeCampaignEcommerceCustomerId = $ecommerceCustomer->getId();
            $linkedExistingEcommerceCustomer = true;
            $this->logger?->warning(sprintf(
                'EcommerceCustomer with email "%s" already exists on ActiveCampaign with id "%s". Why it has not been found before?',
                (string) $customer->getEmail(),
                $activeCampaignEcommerceCustomerId,
            ));
        } catch (\Throwable $e) {
            $this->logger?->error($e->getMessage(), $e->getTrace());

            throw $e;
        }
        $channelCustomer = $this->channelCustomerFactory->createNew();
        $channelCustomer->setCustomer($customer);
        $channelCustomer->setActiveCampaignId($activeCampaignEcommerceCustomerId);
        $channelCustomer->setChannel($channel);
        $this->entityManager->persist($channelCustomer);
        $customer->addChannelCustomer($channelCustomer);
        $this->customerRepository->add($customer);
        if ($linkedExistingEcommerceCustomer) {
            $this->activeCampaignClient->update($activeCampaignEcommerceCustomerId, $this->ecommerceCustomerMapper->mapFromCustomerAndChannel($customer, $channel));
        }
    }
}
