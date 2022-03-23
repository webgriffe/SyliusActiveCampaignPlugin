<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceOrder;

use App\Entity\Order\OrderInterface;
use InvalidArgumentException;
use Sylius\Component\Core\Model\OrderInterface as SyliusOrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\EcommerceOrder\EcommerceOrderUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceOrder\EcommerceOrderUpdateHandler;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\UpdateResourceResponseInterface;

class EcommerceOrderUpdateHandlerSpec extends ObjectBehavior
{
    public function let(
        EcommerceOrderMapperInterface $ecommerceOrderMapper,
        ActiveCampaignResourceClientInterface $activeCampaignEcommerceOrderClient,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        EcommerceOrderInterface $ecommerceOrder
    ): void {
        $ecommerceOrderMapper->mapFromOrder($order, true)->willReturn($ecommerceOrder);

        $order->getActiveCampaignId()->willReturn(364);

        $orderRepository->find(54)->willReturn($order);

        $this->beConstructedWith($ecommerceOrderMapper, $activeCampaignEcommerceOrderClient, $orderRepository);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(EcommerceOrderUpdateHandler::class);
    }

    public function it_throws_if_order_is_not_found(
        OrderRepositoryInterface $orderRepository
    ): void {
        $orderRepository->find(54)->shouldBeCalledOnce()->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('Order with id "54" does not exists.'))->during(
            '__invoke', [new EcommerceOrderUpdate(54, 364, true)]
        );
    }

    public function it_throws_if_order_is_not_an_implementation_of_active_campaign_aware_interface(
        OrderRepositoryInterface $orderRepository,
        SyliusOrderInterface $syliusOrder
    ): void {
        $orderRepository->find(54)->shouldBeCalledOnce()->willReturn($syliusOrder);

        $this->shouldThrow(new InvalidArgumentException('The Order entity should implement the "Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface" class.'))->during(
            '__invoke', [new EcommerceOrderUpdate(54, 364, true)]
        );
    }

    public function it_throws_if_order_not_been_exported_to_active_campaign_yet(
        ActiveCampaignAwareInterface $order
    ): void {
        $order->getActiveCampaignId()->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('The Order with id "54" has an ActiveCampaign id that does not match. Expected "364", given "".'))->during(
            '__invoke', [new EcommerceOrderUpdate(54, 364, true)]
        );
    }

    public function it_throws_if_order_has_a_different_id_on_active_campaign_than_the_message_provided(
        ActiveCampaignAwareInterface $order
    ): void {
        $order->getActiveCampaignId()->willReturn(312);

        $this->shouldThrow(new InvalidArgumentException('The Order with id "54" has an ActiveCampaign id that does not match. Expected "364", given "312".'))->during(
            '__invoke', [new EcommerceOrderUpdate(54, 364, true)]
        );
    }

    public function it_updates_ecommerce_order_on_active_campaign(
        EcommerceOrderInterface $ecommerceOrder,
        ActiveCampaignResourceClientInterface $activeCampaignEcommerceOrderClient,
        UpdateResourceResponseInterface $updateEcommerceOrderResponse
    ): void {
        $activeCampaignEcommerceOrderClient->update(364, $ecommerceOrder)->shouldBeCalledOnce()->willReturn($updateEcommerceOrderResponse);

        $this->__invoke(new EcommerceOrderUpdate(54, 364, true));
    }
}
