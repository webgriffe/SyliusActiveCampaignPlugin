<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use Tests\Webgriffe\SyliusActiveCampaignPlugin\App\Entity\Channel\ChannelInterface;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\App\Entity\Customer\CustomerInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\EcommerceCustomerEnqueuer;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\EcommerceCustomerEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceCustomer\EcommerceCustomerUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer\EcommerceCustomerResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;
use Webmozart\Assert\InvalidArgumentException;

final class EcommerceCustomerEnqueuerSpec extends ObjectBehavior
{
    public function let(
        MessageBusInterface $messageBus,
        ActiveCampaignResourceClientInterface $activeCampaignEcommerceCustomerClient,
        EntityManagerInterface $entityManager,
        FactoryInterface $channelCustomerFactory,
        CustomerInterface $customer,
        ChannelInterface $channel,
        ListResourcesResponseInterface $listResourcesResponse
    ): void {
        $customer->getChannelCustomerByChannel($channel)->willReturn(null);
        $customer->getId()->willReturn(10);
        $customer->getEmail()->willReturn('email@domain.com');

        $channel->getId()->willReturn(1);
        $channel->getActiveCampaignId()->willReturn(111);

        $activeCampaignEcommerceCustomerClient->list([
            'filters[email]' => 'email@domain.com',
            'filters[connectionid]' => '111',
        ])->willReturn($listResourcesResponse);
        $listResourcesResponse->getResourceResponseLists()->willReturn([]);

        $this->beConstructedWith($messageBus, $activeCampaignEcommerceCustomerClient, $entityManager, $channelCustomerFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(EcommerceCustomerEnqueuer::class);
    }

    public function it_implements_ecommerce_customer_enqueuer_interface(): void
    {
        $this->shouldImplement(EcommerceCustomerEnqueuerInterface::class);
    }

    public function it_throws_if_customer_has_no_id(CustomerInterface $customer, ChannelInterface $channel): void
    {
        $customer->getId()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The customer id should not be null'))->during('enqueue', [$customer, $channel]);
    }

    public function it_throws_if_channel_has_no_id(CustomerInterface $customer, ChannelInterface $channel): void
    {
        $channel->getId()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The channel id should not be null'))->during('enqueue', [$customer, $channel]);
    }

    public function it_dispatches_customer_update_message_if_channel_customer_already_exists(
        MessageBusInterface $messageBus,
        CustomerInterface $customer,
        ChannelInterface $channel,
        ChannelCustomerInterface $channelCustomer
    ): void {
        $channelCustomer->getActiveCampaignId()->willReturn(50);
        $customer->getChannelCustomerByChannel($channel)->willReturn($channelCustomer);
        $messageBus
            ->dispatch(Argument::type(EcommerceCustomerUpdate::class))
            ->shouldBeCalledOnce()
            ->willReturn(new Envelope(new EcommerceCustomerUpdate(1, 50, 111)));
        $this->enqueue($customer, $channel);
    }

    public function it_throws_if_customer_has_no_channel_customer_for_channel_and_he_has_no_email(
        MessageBusInterface $messageBus,
        CustomerInterface $customer,
        ChannelInterface $channel
    ): void {
        $customer->getEmail()->willReturn(null);
        $messageBus->dispatch(Argument::any())->shouldNotBeCalled();
        $this->shouldThrow(new InvalidArgumentException('The customer email should not be null'))->during('enqueue', [$customer, $channel]);
    }

    public function it_throws_if_customer_has_no_channel_customer_for_channel_and_this_has_no_active_campaign_id(
        MessageBusInterface $messageBus,
        CustomerInterface $customer,
        ChannelInterface $channel
    ): void {
        $channel->getActiveCampaignId()->willReturn(null);
        $messageBus->dispatch(Argument::any())->shouldNotBeCalled();
        $this->shouldThrow(new InvalidArgumentException('You should export the channel "1" to Active Campaign before enqueuing the customer "email@domain.com"'))->during('enqueue', [$customer, $channel]);
    }

    public function it_dispatches_customer_update_message_if_customer_has_no_channel_customer_but_this_exists_on_active_campaign(
        MessageBusInterface $messageBus,
        EntityManagerInterface $entityManager,
        FactoryInterface $channelCustomerFactory,
        CustomerInterface $customer,
        ChannelInterface $channel,
        ChannelCustomerInterface $channelCustomer,
        ListResourcesResponseInterface $listResourcesResponse
    ): void {
        $ecommerceCustomerResponse = new EcommerceCustomerResponse(999);
        $listResourcesResponse->getResourceResponseLists()->willReturn([$ecommerceCustomerResponse]);
        $channelCustomerFactory->createNew()->shouldBeCalledOnce()->willReturn($channelCustomer);
        $channelCustomer->setActiveCampaignId(999);
        $channelCustomer->setChannel($channel);
        $channelCustomer->setCustomer($customer);
        $customer->addChannelCustomer($channelCustomer)->shouldBeCalledOnce();
        $entityManager->persist($channelCustomer)->shouldBeCalledOnce();
        $entityManager->flush()->shouldBeCalledOnce();
        $messageBus
            ->dispatch(Argument::type(EcommerceCustomerUpdate::class))
            ->shouldBeCalledOnce()
            ->willReturn(new Envelope(new EcommerceCustomerUpdate(1, 999, 111)));

        $this->enqueue($customer, $channel);
    }

    public function it_dispatches_customer_create_message_if_customer_has_no_channel_customer_and_this_does_not_exist_on_active_campaign(
        MessageBusInterface $messageBus,
        CustomerInterface $customer,
        ChannelInterface $channel,
    ): void {
        $messageBus
            ->dispatch(Argument::type(EcommerceCustomerCreate::class))
            ->shouldBeCalledOnce()
            ->willReturn(new Envelope(new EcommerceCustomerCreate(1, 111)));
        $this->enqueue($customer, $channel);
    }
}
