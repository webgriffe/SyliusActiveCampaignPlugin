<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaignContact;

use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaignContact\CreateContactResponseFactory;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaignContact\CreateContactResponseFactoryInterface;

final class CreateContactResponseFactorySpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(CreateContactResponseFactory::class);
    }

    public function it_implements_interface(): void
    {
        $this->shouldImplement(CreateContactResponseFactoryInterface::class);
    }

    public function it_creates_new_from_payload(): void
    {
        $result = $this->createNewFromPayload(
            [
                'fieldValues' => [],
                'email' => 'test@email.com',
                'cdate' => '2022-03-07T10:16:24-06:00',
                'udate' => '2022-03-07T10:16:24-06:00',
                'origid' => 'ABC123',
                'organization' => 'Webgriffe SRL',
                'links' => [],
                'id' => '1',
            ]
        );

        $result->getFieldValues()->shouldReturn([]);
        $result->getEmail()->shouldReturn('test@email.com');
        $result->getCreatedAt()->shouldReturn('2022-03-07T10:16:24-06:00');
        $result->getUpdatedAt()->shouldReturn('2022-03-07T10:16:24-06:00');
        $result->getOriganizationId()->shouldReturn('ABC123');
        $result->getLinks()->shouldReturn([]);
        $result->getId()->shouldReturn('1');
        $result->getOrganization()->shouldReturn('Webgriffe SRL');
    }
}
