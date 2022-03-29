<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use Doctrine\ORM\EntityManagerInterface;
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
    public function __construct(
        private MessageBusInterface $messageBus,
        private ActiveCampaignResourceClientInterface $activeCampaignEcommerceCustomerClient,
        private EntityManagerInterface $entityManager,
        private FactoryInterface $channelCustomerFactory,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function queue($customer, $channel): void
    {
        /** @var string|int|null $customerId */
        $customerId = $customer->getId();
        Assert::notNull($customerId);
        /** @var string|int|null $channelId */
        $channelId = $channel->getId();
        Assert::notNull($channelId);

        $channelCustomer = $customer->getChannelCustomerByChannel($channel);
        if ($channelCustomer !== null) {
            $this->messageBus->dispatch(new EcommerceCustomerUpdate($customerId, $channelCustomer->getActiveCampaignId(), $channelId));

            return;
        }

        $email = $customer->getEmail();
        Assert::notNull($email);
        $activeCampaignChannelId = $channel->getActiveCampaignId();
        Assert::notNull($activeCampaignChannelId);

        $ecommerceCustomerList = $this->activeCampaignEcommerceCustomerClient->list([
            'filters[email]' => $email,
            'filters[connectionid]' => (string) $activeCampaignChannelId,
        ])->getResourceResponseLists();
        if (count($ecommerceCustomerList) > 0) {
            /** @var EcommerceCustomerResponse $ecommerceCustomer */
            $ecommerceCustomer = reset($ecommerceCustomerList);
            $activeCampaignEcommerceCustomerId = $ecommerceCustomer->getId();
            /** @var ChannelCustomerInterface $channelCustomer */
            $channelCustomer = $this->channelCustomerFactory->createNew();
            $channelCustomer->setActiveCampaignId($activeCampaignEcommerceCustomerId);
            $channelCustomer->setChannel($channel);
            $channelCustomer->setCustomer($customer);
            $this->entityManager->persist($channelCustomer);
            $this->entityManager->flush();

            $this->messageBus->dispatch(new EcommerceCustomerUpdate($customerId, $activeCampaignEcommerceCustomerId, $channelId));

            return;
        }

        $this->messageBus->dispatch(new EcommerceCustomerCreate($customerId, $channelId));
    }
}
