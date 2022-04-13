<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Mapper;

use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\TagFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\TagMapper;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\TagMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\TagInterface;

class TagMapperSpec extends ObjectBehavior
{
    public function let(TagFactoryInterface $tagFactory, TagInterface $tag): void
    {
        $tagFactory->createNew('male', TagInterface::CONTACT_TAG_TYPE)->willReturn($tag);

        $this->beConstructedWith($tagFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(TagMapper::class);
    }

    public function it_implements_tag_mapper_interface(): void
    {
        $this->shouldImplement(TagMapperInterface::class);
    }

    public function it_should_returns_tag(TagInterface $tag): void
    {
        $this->mapFromTagAndTagType('male')->shouldReturn($tag);
    }
}
