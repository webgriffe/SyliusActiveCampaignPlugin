<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign;

use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\TagFactory;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\TagFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Tag;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\TagInterface;

class TagFactorySpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith();
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(TagFactory::class);
    }

    public function it_implements_tag_factory_interface(): void
    {
        $this->shouldImplement(TagFactoryInterface::class);
    }

    public function it_should_returns_a_tag_instance(): void
    {
        $this->createNew(
            'male',
            TagInterface::CONTACT_TAG_TYPE
        )->shouldReturnAnInstanceOf(Tag::class);
    }
}
