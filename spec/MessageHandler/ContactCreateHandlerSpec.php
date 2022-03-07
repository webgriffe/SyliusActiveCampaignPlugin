<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\ContactCreateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignContactInterface;

class ContactCreateHandlerSpec extends ObjectBehavior
{
    public function let(
        ContactMapperInterface $contactMapper,
        ActiveCampaignContactInterface $contact,
        CustomerInterface $customer
    ): void {
        $contactMapper->mapFromCustomer($customer)->willReturn($contact);

        $this->beConstructedWith();
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ContactCreateHandler::class);
    }
}
