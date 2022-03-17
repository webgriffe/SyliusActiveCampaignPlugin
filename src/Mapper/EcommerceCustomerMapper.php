<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\ChannelConnectionNotSetException;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\CustomerDoesNotHaveEmailException;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceCustomerFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceCustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;

final class EcommerceCustomerMapper implements EcommerceCustomerMapperInterface
{
    public function __construct(
        private EcommerceCustomerFactoryInterface $ecommerceCustomerFactory
    ) {
    }

    public function mapFromCustomerAndChannel(CustomerInterface $customer, ActiveCampaignAwareInterface $channel): EcommerceCustomerInterface
    {
        $connectionId = $channel->getActiveCampaignId();
        if ($connectionId === null) {
            throw new ChannelConnectionNotSetException();
        }

        /** @var string|int|null $customerId */
        $customerId = $customer->getId();
        $customerEmail = $customer->getEmail();
        if ($customerEmail === null) {
            throw new CustomerDoesNotHaveEmailException(sprintf(
                'Unable to create a new ActiveCampaign Ecommerce Customer, the customer "%s" does not have a valid email.',
                (string) $customerId
            ));
        }
        $contact = $this->ecommerceCustomerFactory->createNew($customerEmail, (string) $connectionId, (string) $customerId);
        $contact->setAcceptsMarketing($customer->isSubscribedToNewsletter() ? '1' : '0');

        return $contact;
    }
}
