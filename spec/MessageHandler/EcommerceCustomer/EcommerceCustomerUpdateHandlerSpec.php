<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer;

use Tests\Webgriffe\SyliusActiveCampaignPlugin\App\Entity\Channel\ChannelInterface;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\App\Entity\Customer\CustomerInterface;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface as SyliusChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface as SyliusCustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceCustomerMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer\EcommerceCustomerUpdateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceCustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

final class EcommerceCustomerUpdateHandlerSpec extends ObjectBehavior
{
    public function let(
        EcommerceCustomerMapperInterface $ecommerceCustomerMapper,
        ActiveCampaignResourceClientInterface $activeCampaignClient,
        CustomerRepositoryInterface $customerRepository,
        ChannelRepositoryInterface $channelRepository,
        CustomerInterface $customer,
        ChannelInterface $channel,
        EcommerceCustomerInterface $ecommerceCustomer,
        ChannelCustomerInterface $channelCustomer
    ): void {
        $ecommerceCustomerMapper->mapFromCustomerAndChannel($customer, $channel)->willReturn($ecommerceCustomer);

        $channel->getActiveCampaignId()->willReturn(567);
        $channel->getCode()->willReturn('ecommerce');

        $customer->getActiveCampaignId()->willReturn(3423);
        $customer->getChannelCustomerByChannel($channel)->willReturn($channelCustomer);

        $channelCustomer->getActiveCampaignId()->willReturn(3423);

        $channelRepository->find(1)->willReturn($channel);
        $customerRepository->find(12)->willReturn($customer);

        $this->beConstructedWith($ecommerceCustomerMapper, $activeCampaignClient, $customerRepository, $channelRepository);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(EcommerceCustomerUpdateHandler::class);
    }

    public function it_throws_if_channel_is_not_found(
        ChannelRepositoryInterface $channelRepository
    ): void {
        $channelRepository->find(1)->shouldBeCalledOnce()->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('Channel with id "1" does not exists'))->during(
            '__invoke',
            [new EcommerceCustomerUpdate(12, 3423, 1)]
        );
    }

    public function it_throws_if_channel_is_not_an_implementation_of_active_campaign_aware_interface(
        ChannelRepositoryInterface $channelRepository,
        SyliusChannelInterface $syliusChannel
    ): void {
        $channelRepository->find(1)->shouldBeCalledOnce()->willReturn($syliusChannel);

        $this->shouldThrow(new InvalidArgumentException('The Channel entity should implement the "Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface" class'))->during(
            '__invoke',
            [new EcommerceCustomerUpdate(12, 3423, 1)]
        );
    }

    public function it_throws_if_customer_is_not_found(
        CustomerRepositoryInterface $customerRepository
    ): void {
        $customerRepository->find(12)->shouldBeCalledOnce()->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('Customer with id "12" does not exists'))->during(
            '__invoke',
            [new EcommerceCustomerUpdate(12, 3423, 1)]
        );
    }

    public function it_throws_if_customer_is_not_an_implementation_of_customer_active_campaign_aware_interface(
        CustomerRepositoryInterface $customerRepository,
        SyliusCustomerInterface $syliusCustomer
    ): void {
        $customerRepository->find(12)->shouldBeCalledOnce()->willReturn($syliusCustomer);

        $this->shouldThrow(new InvalidArgumentException('The Customer entity should implement the "Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface" class'))->during(
            '__invoke',
            [new EcommerceCustomerUpdate(12, 3423, 1)]
        );
    }

    public function it_throws_if_customer_has_not_been_exported_to_active_campaign_yet(
        CustomerActiveCampaignAwareInterface $customer,
        ChannelInterface $channel
    ): void {
        $customer->getChannelCustomerByChannel($channel)->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('The Customer with id "12" does not have an ActiveCampaign Ecommerce customer for the channel "ecommerce".'))->during(
            '__invoke',
            [new EcommerceCustomerUpdate(12, 3423, 1)]
        );
    }

    public function it_throws_if_customer_has_an_active_campaign_id_that_differs_from_the_one_on_the_message(
        CustomerActiveCampaignAwareInterface $customer,
        ChannelInterface $channel,
        ChannelCustomerInterface $channelCustomer
    ): void {
        $customer->getChannelCustomerByChannel($channel)->willReturn($channelCustomer);
        $channelCustomer->getActiveCampaignId()->willReturn(432);

        $this->shouldThrow(new InvalidArgumentException('The Customer with id "12" has an ActiveCampaign id that does not match. Expected "3423", given "432".'))->during(
            '__invoke',
            [new EcommerceCustomerUpdate(12, 3423, 1)]
        );
    }

    public function it_updates_ecommerce_customer_on_active_campaign(
        ActiveCampaignResourceClientInterface $activeCampaignClient,
        EcommerceCustomerInterface $ecommerceCustomer,
        UpdateResourceResponseInterface $updateEcommerceCustomerResponse
    ): void {
        $activeCampaignClient->update(3423, $ecommerceCustomer)->shouldBeCalledOnce()->willReturn($updateEcommerceCustomerResponse);

        $this->__invoke(new EcommerceCustomerUpdate(12, 3423, 1));
    }
}
