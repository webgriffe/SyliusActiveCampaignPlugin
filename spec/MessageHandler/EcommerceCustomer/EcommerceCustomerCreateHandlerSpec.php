<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer;

use App\Entity\Channel\ChannelInterface;
use App\Entity\Customer\CustomerInterface;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\CustomerInterface as SyliusCustomerInterface;
use Sylius\Component\Core\Model\ChannelInterface as SyliusChannelInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceCustomerMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerCreate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer\EcommerceCustomerCreateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceCustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;

final class EcommerceCustomerCreateHandlerSpec extends ObjectBehavior
{
    public function let(
        EcommerceCustomerMapperInterface $ecommerceCustomerMapper,
        ActiveCampaignResourceClientInterface $activeCampaignClient,
        CustomerRepositoryInterface $customerRepository,
        ChannelRepositoryInterface $channelRepository,
        CustomerInterface $customer,
        ChannelInterface $channel,
        EcommerceCustomerInterface $ecommerceCustomer
    ): void {
        $ecommerceCustomerMapper->mapFromCustomerAndChannel($customer, $channel)->willReturn($ecommerceCustomer);

        $channel->getActiveCampaignId()->willReturn(567);
        $customer->getActiveCampaignId()->willReturn(null);

        $channelRepository->find(1)->willReturn($channel);
        $customerRepository->find(12)->willReturn($customer);

        $this->beConstructedWith($ecommerceCustomerMapper, $activeCampaignClient, $customerRepository, $channelRepository);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(EcommerceCustomerCreateHandler::class);
    }

    public function it_throws_if_channel_is_not_found(
        ChannelRepositoryInterface $channelRepository
    ): void {
        $channelRepository->find(1)->shouldBeCalledOnce()->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('Channel with id "1" does not exists'))->during(
            '__invoke', [new EcommerceCustomerCreate(12, 1)]
        );
    }

    public function it_throws_if_channel_is_not_an_implementation_of_active_campaign_aware_interface(
        ChannelRepositoryInterface $channelRepository,
        SyliusChannelInterface $syliusChannel
    ): void {
        $channelRepository->find(1)->shouldBeCalledOnce()->willReturn($syliusChannel);

        $this->shouldThrow(new InvalidArgumentException('The Channel entity should implement the "Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface" class'))->during(
            '__invoke', [new EcommerceCustomerCreate(12, 1)]
        );
    }

    public function it_throws_if_customer_is_not_found(
        CustomerRepositoryInterface $customerRepository
    ): void {
        $customerRepository->find(12)->shouldBeCalledOnce()->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('Customer with id "12" does not exists'))->during(
            '__invoke', [new EcommerceCustomerCreate(12, 1)]
        );
    }

    public function it_throws_if_customer_is_not_an_implementation_of_active_campaign_aware_interface(
        CustomerRepositoryInterface $customerRepository,
        SyliusCustomerInterface $syliusCustomer
    ): void {
        $customerRepository->find(12)->shouldBeCalledOnce()->willReturn($syliusCustomer);

        $this->shouldThrow(new InvalidArgumentException('The Customer entity should implement the "Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface" class'))->during(
            '__invoke', [new EcommerceCustomerCreate(12, 1)]
        );
    }

    public function it_throws_if_customer_has_been_already_exported_to_active_campaign(
        ActiveCampaignAwareInterface $customer
    ): void {
        $customer->getActiveCampaignId()->willReturn('321');

        $this->shouldThrow(new InvalidArgumentException('The Customer with id "12" has been already created on ActiveCampaign on the EcommerceCustomer with id "321"'))->during(
            '__invoke', [new EcommerceCustomerCreate(12, 1)]
        );
    }

    public function it_creates_ecommerce_customer_on_active_campaign(
        ActiveCampaignResourceClientInterface $activeCampaignClient,
        CustomerInterface $customer,
        CustomerRepositoryInterface $customerRepository,
        EcommerceCustomerInterface $ecommerceCustomer,
        CreateResourceResponseInterface $createEcommerceCustomerResponse,
        ResourceResponseInterface $ecommerceCustomerResponse
    ): void {
        $ecommerceCustomerResponse->getId()->willReturn(3423);
        $createEcommerceCustomerResponse->getResourceResponse()->willReturn($ecommerceCustomerResponse);
        $activeCampaignClient->create($ecommerceCustomer)->shouldBeCalledOnce()->willReturn($createEcommerceCustomerResponse);
        $customer->setActiveCampaignId(3423)->shouldBeCalledOnce();
        $customerRepository->add($customer)->shouldBeCalledOnce();

        $this->__invoke(new EcommerceCustomerCreate(12, 1));
    }
}
