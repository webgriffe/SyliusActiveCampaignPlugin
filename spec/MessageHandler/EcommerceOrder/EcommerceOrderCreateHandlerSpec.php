<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceOrder;

use Sylius\Component\Core\OrderPaymentStates;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Entity\Channel\ChannelInterface;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Entity\Order\OrderInterface;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface as SyliusOrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceOrder\EcommerceOrderCreateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder\EcommerceOrderResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;

class EcommerceOrderCreateHandlerSpec extends ObjectBehavior
{
    public function let(
        EcommerceOrderMapperInterface $ecommerceOrderMapper,
        ActiveCampaignResourceClientInterface $activeCampaignEcommerceOrderClient,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        EcommerceOrderInterface $ecommerceOrder,
        ChannelInterface $channel,
        MessageBusInterface $messageBus,
    ): void {
        $ecommerceOrderMapper->mapFromOrder($order, true)->willReturn($ecommerceOrder);

        $channel->getActiveCampaignId()->willReturn(321);

        $order->getActiveCampaignId()->willReturn(null);
        $order->getChannel()->willReturn($channel);
        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_PAID);

        $orderRepository->find(54)->willReturn($order);

        $this->beConstructedWith($ecommerceOrderMapper, $activeCampaignEcommerceOrderClient, $orderRepository, null, $messageBus);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(EcommerceOrderCreateHandler::class);
    }

    public function it_throws_if_order_is_not_found(
        OrderRepositoryInterface $orderRepository
    ): void {
        $orderRepository->find(54)->shouldBeCalledOnce()->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('Order with id "54" does not exists'))->during(
            '__invoke',
            [new EcommerceOrderCreate(54, true)]
        );
    }

    public function it_throws_if_order_is_not_an_implementation_of_active_campaign_aware_interface(
        OrderRepositoryInterface $orderRepository,
        SyliusOrderInterface $syliusOrder
    ): void {
        $orderRepository->find(54)->shouldBeCalledOnce()->willReturn($syliusOrder);

        $this->shouldThrow(new InvalidArgumentException('The Order entity should implement the "Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface" class'))->during(
            '__invoke',
            [new EcommerceOrderCreate(54, true)]
        );
    }

    public function it_logs_warning_and_returns_if_order_has_been_already_exported_to_active_campaign(
        OrderInterface $order,
        ActiveCampaignResourceClientInterface $activeCampaignEcommerceOrderClient,
        OrderRepositoryInterface $orderRepository,
    ): void {
        $order->getActiveCampaignId()->willReturn(65);

        $activeCampaignEcommerceOrderClient->create(\Prophecy\Argument::any())->shouldNotBeCalled();
        $orderRepository->add(\Prophecy\Argument::any())->shouldNotBeCalled();

        $this->__invoke(new EcommerceOrderCreate(54, true));
    }

    public function it_links_existing_ecommerce_order_on_active_campaign_when_creation_returns_unprocessable_entity(
        EcommerceOrderInterface $ecommerceOrder,
        ActiveCampaignResourceClientInterface $activeCampaignEcommerceOrderClient,
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        ListResourcesResponseInterface $searchOrdersResponse,
        MessageBusInterface $messageBus,
    ): void {
        $existingOrderResponse = new EcommerceOrderResponse(777);
        $activeCampaignEcommerceOrderClient->create($ecommerceOrder)->shouldBeCalledOnce()->willThrow(new UnprocessableEntityHttpException());
        $activeCampaignEcommerceOrderClient->list([
            'filters[connectionid]' => '321',
            'filters[externalid]' => '54',
        ])->shouldBeCalledOnce()->willReturn($searchOrdersResponse);
        $searchOrdersResponse->getResourceResponseLists()->willReturn([$existingOrderResponse]);

        $order->setActiveCampaignId(777)->shouldBeCalledOnce();
        $orderRepository->add($order)->shouldBeCalledOnce();
        $messageBus->dispatch(Argument::type(EcommerceOrderUpdate::class))->shouldBeCalledOnce()->willReturn(new Envelope(new EcommerceOrderUpdate(54, 777, true)));

        $this->__invoke(new EcommerceOrderCreate(54, true));
    }

    public function it_creates_ecommerce_order_on_active_campaign(
        EcommerceOrderInterface $ecommerceOrder,
        ActiveCampaignResourceClientInterface $activeCampaignEcommerceOrderClient,
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        CreateResourceResponseInterface $createEcommerceOrderResponse,
        ResourceResponseInterface $ecommerceOrderResponse
    ): void {
        $ecommerceOrderResponse->getId()->willReturn(432);
        $createEcommerceOrderResponse->getResourceResponse()->willReturn($ecommerceOrderResponse);
        $activeCampaignEcommerceOrderClient->create($ecommerceOrder)->shouldBeCalledOnce()->willReturn($createEcommerceOrderResponse);
        $order->setActiveCampaignId(432)->shouldBeCalledOnce();
        $orderRepository->add($order)->shouldBeCalledOnce();

        $this->__invoke(new EcommerceOrderCreate(54, true));
    }
}
