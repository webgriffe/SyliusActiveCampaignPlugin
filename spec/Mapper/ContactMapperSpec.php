<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactMapper;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactMapperInterface;

class ContactMapperSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith();
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ContactMapper::class);
    }

    public function it_implements_contact_mapper_interface(): void
    {
        $this->shouldImplement(ContactMapperInterface::class);
    }
}
