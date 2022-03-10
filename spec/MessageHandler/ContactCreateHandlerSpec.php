<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\ContactCreateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactInterface;

class ContactCreateHandlerSpec extends ObjectBehavior
{
    public function let(
        ContactMapperInterface $contactMapper,
        ContactInterface $contact,
        CustomerInterface $customer
    ): void {
        $contactMapper->mapFromCustomer($customer)->willReturn($contact);

        $this->beConstructedWith($contactMapper);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ContactCreateHandler::class);
    }
}
