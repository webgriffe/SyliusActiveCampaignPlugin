<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceOrder;

use App\Entity\Order\OrderInterface;
use InvalidArgumentException;
use Sylius\Component\Core\Model\OrderInterface as SyliusOrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderCreate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceOrder\EcommerceOrderCreateHandler;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder\EcommerceOrderResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder\CreateEcommerceOrderResponse;

class EcommerceOrderCreateHandlerSpec extends ObjectBehavior
{
    public function let(
        EcommerceOrderMapperInterface $ecommerceOrderMapper,
        ActiveCampaignResourceClientInterface $activeCampaignEcommerceOrderClient,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        EcommerceOrderInterface $ecommerceOrder
    ): void {
        $ecommerceOrderMapper->mapFromOrder($order, true)->willReturn($ecommerceOrder);

        $order->getActiveCampaignId()->willReturn(null);

        $orderRepository->find(54)->willReturn($order);

        $this->beConstructedWith($ecommerceOrderMapper, $activeCampaignEcommerceOrderClient, $orderRepository);
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
            '__invoke', [new EcommerceOrderCreate(54, true)]
        );
    }

    public function it_throws_if_order_is_not_an_implementation_of_active_campaign_aware_interface(
        OrderRepositoryInterface $orderRepository,
        SyliusOrderInterface $syliusOrder
    ): void {
        $orderRepository->find(54)->shouldBeCalledOnce()->willReturn($syliusOrder);

        $this->shouldThrow(new InvalidArgumentException('The Order entity should implement the "Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface" class'))->during(
            '__invoke', [new EcommerceOrderCreate(54, true)]
        );
    }

    public function it_throws_if_order_has_been_already_exported_to_active_campaign(
        ActiveCampaignAwareInterface $order
    ): void {
        $order->getActiveCampaignId()->willReturn(65);

        $this->shouldThrow(new InvalidArgumentException('The Order with id "54" has been already created on ActiveCampaign on the ecommerce order with id "65"'))->during(
            '__invoke', [new EcommerceOrderCreate(54, true)]
        );
    }

    public function it_creates_ecommerce_order_on_active_campaign(
        EcommerceOrderInterface $ecommerceOrder,
        ActiveCampaignResourceClientInterface $activeCampaignEcommerceOrderClient,
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository
    ): void {
        $activeCampaignEcommerceOrderClient->create($ecommerceOrder)->shouldBeCalledOnce()->willReturn(new CreateEcommerceOrderResponse(new EcommerceOrderResponse(432)));
        $order->setActiveCampaignId(432)->shouldBeCalledOnce();
        $orderRepository->add($order)->shouldBeCalledOnce();

        $this->__invoke(new EcommerceOrderCreate(54, true));
    }
}
