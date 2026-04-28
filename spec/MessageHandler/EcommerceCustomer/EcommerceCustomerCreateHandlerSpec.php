<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer;

use Tests\Webgriffe\SyliusActiveCampaignPlugin\Entity\Channel\ChannelInterface;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Entity\Customer\CustomerInterface;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface as SyliusChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface as SyliusCustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceCustomerMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerCreate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer\EcommerceCustomerCreateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceCustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer\EcommerceCustomerResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;
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
        EcommerceCustomerInterface $ecommerceCustomer,
        FactoryInterface $channelCustomerFactory,
        ChannelCustomerInterface $channelCustomer,
        EntityManagerInterface $entityManager
    ): void {
        $ecommerceCustomerMapper->mapFromCustomerAndChannel($customer, $channel)->willReturn($ecommerceCustomer);

        $channel->getActiveCampaignId()->willReturn(567);
        $customer->getActiveCampaignId()->willReturn(null);
        $customer->getChannelCustomerByChannel($channel)->willReturn(null);

        $channelRepository->find(1)->willReturn($channel);
        $customerRepository->find(12)->willReturn($customer);

        $channelCustomerFactory->createNew()->willReturn($channelCustomer);

        $this->beConstructedWith($ecommerceCustomerMapper, $activeCampaignClient, $customerRepository, $channelRepository, $channelCustomerFactory, $entityManager);
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
            '__invoke',
            [new EcommerceCustomerCreate(12, 1)]
        );
    }

    public function it_throws_if_channel_is_not_an_implementation_of_active_campaign_aware_interface(
        ChannelRepositoryInterface $channelRepository,
        SyliusChannelInterface $syliusChannel
    ): void {
        $channelRepository->find(1)->shouldBeCalledOnce()->willReturn($syliusChannel);

        $this->shouldThrow(new InvalidArgumentException('The Channel entity should implement the "Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface" class'))->during(
            '__invoke',
            [new EcommerceCustomerCreate(12, 1)]
        );
    }

    public function it_throws_if_customer_is_not_found(
        CustomerRepositoryInterface $customerRepository
    ): void {
        $customerRepository->find(12)->shouldBeCalledOnce()->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('Customer with id "12" does not exists'))->during(
            '__invoke',
            [new EcommerceCustomerCreate(12, 1)]
        );
    }

    public function it_throws_if_customer_is_not_an_implementation_of_customer_active_campaign_aware_interface(
        CustomerRepositoryInterface $customerRepository,
        SyliusCustomerInterface $syliusCustomer
    ): void {
        $customerRepository->find(12)->shouldBeCalledOnce()->willReturn($syliusCustomer);

        $this->shouldThrow(new InvalidArgumentException('The Customer entity should implement the "Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface" class'))->during(
            '__invoke',
            [new EcommerceCustomerCreate(12, 1)]
        );
    }

    public function it_logs_warning_and_returns_if_customer_has_been_already_exported_to_active_campaign(
        ActiveCampaignResourceClientInterface $activeCampaignClient,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterface $customer,
        ChannelInterface $channel,
        ChannelCustomerInterface $channelCustomer,
    ): void {
        $customer->getChannelCustomerByChannel($channel)->willReturn($channelCustomer);
        $channelCustomer->getActiveCampaignId()->willReturn(321);

        $activeCampaignClient->create(\Prophecy\Argument::any())->shouldNotBeCalled();
        $customerRepository->add(\Prophecy\Argument::any())->shouldNotBeCalled();

        $this->__invoke(new EcommerceCustomerCreate(12, 1));
    }

    public function it_links_existing_ecommerce_customer_on_active_campaign_when_creation_returns_unprocessable_entity(
        ActiveCampaignResourceClientInterface $activeCampaignClient,
        CustomerInterface $customer,
        CustomerRepositoryInterface $customerRepository,
        EcommerceCustomerInterface $ecommerceCustomer,
        ListResourcesResponseInterface $listEcommerceCustomersResponse,
        ChannelCustomerInterface $channelCustomer,
        ChannelInterface $channel,
        EntityManagerInterface $entityManager,
    ): void {
        $activeCampaignClient->create($ecommerceCustomer)->shouldBeCalledOnce()->willThrow(new UnprocessableEntityHttpException('duplicate'));
        $customer->getEmail()->willReturn('test@example.com');

        $existingEcomCustomer = new EcommerceCustomerResponse(9999);
        $listEcommerceCustomersResponse->getResourceResponseLists()->willReturn([$existingEcomCustomer]);
        $activeCampaignClient->list([
            'filters[email]' => 'test@example.com',
            'filters[connectionid]' => 567,
        ])->shouldBeCalledOnce()->willReturn($listEcommerceCustomersResponse);

        $channelCustomer->setCustomer($customer)->shouldBeCalledOnce();
        $channelCustomer->setActiveCampaignId(9999)->shouldBeCalledOnce();
        $channelCustomer->setChannel($channel)->shouldBeCalledOnce();

        $customer->addChannelCustomer($channelCustomer)->shouldBeCalledOnce();
        $entityManager->persist($channelCustomer)->shouldBeCalledOnce();
        $customerRepository->add($customer)->shouldBeCalledOnce();

        $this->__invoke(new EcommerceCustomerCreate(12, 1));
    }

    public function it_creates_ecommerce_customer_on_active_campaign(
        ActiveCampaignResourceClientInterface $activeCampaignClient,
        CustomerInterface $customer,
        CustomerRepositoryInterface $customerRepository,
        EcommerceCustomerInterface $ecommerceCustomer,
        CreateResourceResponseInterface $createEcommerceCustomerResponse,
        ResourceResponseInterface $ecommerceCustomerResponse,
        ChannelCustomerInterface $channelCustomer,
        ChannelInterface $channel,
        EntityManagerInterface $entityManager
    ): void {
        $ecommerceCustomerResponse->getId()->willReturn(3423);
        $createEcommerceCustomerResponse->getResourceResponse()->willReturn($ecommerceCustomerResponse);
        $activeCampaignClient->create($ecommerceCustomer)->shouldBeCalledOnce()->willReturn($createEcommerceCustomerResponse);

        $channelCustomer->setCustomer($customer)->shouldBeCalledOnce();
        $channelCustomer->setActiveCampaignId(3423)->shouldBeCalledOnce();
        $channelCustomer->setChannel($channel)->shouldBeCalledOnce();

        $customer->setActiveCampaignId(Argument::any())->shouldNotBeCalled();
        $customer->addChannelCustomer($channelCustomer)->shouldBeCalledOnce();

        $entityManager->persist($channelCustomer)->shouldBeCalledOnce();

        $customerRepository->add($customer)->shouldBeCalledOnce();

        $this->__invoke(new EcommerceCustomerCreate(12, 1));
    }
}
