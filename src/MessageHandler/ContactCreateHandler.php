<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler;

use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\ContactCreate;

final class ContactCreateHandler
{
    public function __construct(
        private ContactMapperInterface $contactMapper
    ) {
    }

    public function __invoke(ContactCreate $message): void
    {
//        $customer = $this->customerRepository->find($message->getCustomerId());
//
//        if ($customer->getActiveCampaignId() === null) {
//            $this->client->createContact($this->contactMapper->mapFromCustomer($customer));
//        }
//        $this->client->updateContact($this->contactMapper->mapFromCustomer($customer));
    }
}
