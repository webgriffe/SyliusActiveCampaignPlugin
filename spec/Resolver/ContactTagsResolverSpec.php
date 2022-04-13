<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\ContactTagsResolver;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\ContactTagsResolverInterface;

class ContactTagsResolverSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith();
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ContactTagsResolver::class);
    }

    public function it_implements_contact_tags_resolver_interface(): void
    {
        $this->shouldImplement(ContactTagsResolverInterface::class);
    }

    public function it_returns_an_empty_array(CustomerInterface $customer): void
    {
        $this->resolve($customer)->shouldReturn([]);
    }
}
