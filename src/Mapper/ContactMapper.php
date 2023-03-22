<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Exception\CustomerDoesNotHaveEmailException;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ContactFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\FieldValueInterface;
use Webmozart\Assert\Assert;

final class ContactMapper implements ContactMapperInterface
{
    public function __construct(
        private ContactFactoryInterface $contactFactory,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function mapFromCustomer(CustomerInterface $customer): ContactInterface
    {
        $customerEmail = $customer->getEmail();
        if ($customerEmail === null) {
            throw new CustomerDoesNotHaveEmailException(sprintf(
                'Unable to create a new ActiveCampaign Contact, the customer "%s" does not have a valid email.',
                (string) $customer->getId(),
            ));
        }
        $contact = $this->contactFactory->createNewFromEmail($customerEmail);
        $contact->setFirstName($customer->getFirstName());
        $contact->setLastName($customer->getLastName());
        $contact->setPhone($customer->getPhoneNumber());

        /** @var FieldValueInterface[] $fieldValues */
        $fieldValues = [];
        /** @var GenericEvent|mixed $event */
        $event = $this->eventDispatcher->dispatch(new GenericEvent($customer, ['fieldValues' => $fieldValues]), 'webgriffe.sylius_active_campaign_plugin.mapper.customer.pre_add_field_values');
        Assert::isInstanceOf($event, GenericEvent::class);
        /** @var FieldValueInterface[]|mixed $fieldValues */
        $fieldValues = $event->getArgument('fieldValues');
        Assert::isArray($fieldValues, 'The field values should be an array.');
        Assert::allIsInstanceOf($fieldValues, FieldValueInterface::class, sprintf('The field values should be an array of "%s".', FieldValueInterface::class));
        $contact->setFieldValues($fieldValues);

        return $contact;
    }
}
