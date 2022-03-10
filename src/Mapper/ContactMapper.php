<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\CustomerDoesNotHaveEmailException;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaignContactFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;

final class ContactMapper implements ContactMapperInterface
{
    public function __construct(
        private ActiveCampaignContactFactoryInterface $contactFactory
    ) {
    }

    public function mapFromCustomer(CustomerInterface $customer): ContactInterface
    {
        $customerEmail = $customer->getEmail();
        if ($customerEmail === null) {
            throw new CustomerDoesNotHaveEmailException(sprintf(
                'Unable to create a new ActiveCampaign Contact, the customer "%s" does not have a valid email.',
                (string) $customer->getId()
            ));
        }
        $contact = $this->contactFactory->createNewFromEmail($customerEmail);
        $contact->setFirstName($customer->getFirstName());
        $contact->setLastName($customer->getLastName());
        $customerPhoneNumber = $customer->getPhoneNumber();
        if ($customerPhoneNumber !== null) {
            $contact->setPhone((int) str_replace([' ', '-', '/', '*', '+'], '', $customerPhoneNumber));
        }

        return $contact;
    }
}
