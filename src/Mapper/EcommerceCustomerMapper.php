<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\ChannelConnectionNotSetException;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\CustomerDoesNotHaveEmailException;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceCustomerFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceCustomerInterface;

final class EcommerceCustomerMapper implements EcommerceCustomerMapperInterface
{
    public function __construct(
        private EcommerceCustomerFactoryInterface $ecommerceCustomerFactory
    ) {
    }

    public function mapFromCustomerAndChannel(CustomerInterface $customer, $channel): EcommerceCustomerInterface
    {
        $connectionId = $channel->getActiveCampaignId();
        if ($connectionId === null) {
            throw new ChannelConnectionNotSetException(sprintf(
                'Unable to create a new ActiveCampaign Ecommerce Customer, the channel "%s" does not have a connection id. Please, create the connection from the channel before create the ecommerce customer for the channel.',
                (string) $channel->getCode()
            ));
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
        $ecommerceCustomer = $this->ecommerceCustomerFactory->createNew($customerEmail, (string) $connectionId, (string) $customerId);
        $ecommerceCustomer->setAcceptsMarketing($customer->isSubscribedToNewsletter() ? EcommerceCustomerInterface::MARKETING_OPTED_IN : EcommerceCustomerInterface::MARKETING_NOT_OPTED_IN);

        return $ecommerceCustomer;
    }
}
