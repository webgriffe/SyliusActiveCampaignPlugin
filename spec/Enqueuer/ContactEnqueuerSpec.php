<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\Enqueuer;

use App\Entity\Customer\CustomerInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\ContactEnqueuer;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\ContactEnqueuerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactUpdate;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\ContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;
use Webmozart\Assert\InvalidArgumentException;

class ContactEnqueuerSpec extends ObjectBehavior
{
    public function let(
        MessageBusInterface $messageBus,
        ActiveCampaignResourceClientInterface $activeCampaignContactClient,
        EntityManagerInterface $entityManager,
        CustomerInterface $customer,
        ListResourcesResponseInterface $listResourcesResponse
    ): void {
        $customer->getId()->willReturn(1);
        $customer->getActiveCampaignId()->willReturn(null);
        $customer->getEmail()->willReturn('info@eactivecampaign.com');

        $activeCampaignContactClient->list(['email' => 'info@eactivecampaign.com'])->willReturn($listResourcesResponse);

        $listResourcesResponse->getResourceResponseLists()->willReturn([]);

        $this->beConstructedWith($messageBus, $activeCampaignContactClient, $entityManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ContactEnqueuer::class);
    }

    public function it_implements_contact_enqueuer_interface(): void
    {
        $this->shouldImplement(ContactEnqueuerInterface::class);
    }

    public function it_throws_if_customer_id_is_null(CustomerInterface $customer): void
    {
        $customer->getId()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The customer id should not be null'))
            ->during('enqueue', [$customer]);
    }

    public function it_enqueues_a_contact_update_if_customer_contact_active_campaign_id_is_not_null(
        CustomerInterface $customer,
        MessageBusInterface $messageBus
    ): void {
        $customer->getActiveCampaignId()->willReturn(10);
        $messageBus->dispatch(Argument::type(ContactUpdate::class))->shouldBeCalledOnce()->willReturn(new Envelope(new ContactUpdate(1, 10)));

        $this->enqueue($customer);
    }

    public function it_throws_if_customer_email_is_null(CustomerInterface $customer): void
    {
        $customer->getEmail()->willReturn(null);
        $this->shouldThrow(new InvalidArgumentException('The customer email should not be null'))
            ->during('enqueue', [$customer]);
    }

    public function it_enqueues_a_contact_update_if_customer_contact_active_campaign_id_is_null_and_a_contact_with_the_same_email_already_exists(
        CustomerInterface $customer,
        MessageBusInterface $messageBus,
        ListResourcesResponseInterface $listResourcesResponse,
        EntityManagerInterface $entityManager
    ): void {
        $listResourcesResponse->getResourceResponseLists()->willReturn([
            new ContactResponse(14),
        ]);
        $customer->setActiveCampaignId(14)->shouldBeCalledOnce();
        $entityManager->flush()->shouldBeCalledOnce();
        $messageBus->dispatch(Argument::type(ContactUpdate::class))->shouldBeCalledOnce()->willReturn(new Envelope(new ContactUpdate(1, 14)));

        $this->enqueue($customer);
    }

    public function it_enqueues_a_contact_update_using_the_first_match_if_customer_contact_active_campaign_id_is_null_and_a_contact_with_the_same_email_already_exists(
        CustomerInterface $customer,
        MessageBusInterface $messageBus,
        ListResourcesResponseInterface $listResourcesResponse,
        EntityManagerInterface $entityManager
    ): void {
        $listResourcesResponse->getResourceResponseLists()->willReturn([
            new ContactResponse(18),
            new ContactResponse(14),
        ]);
        $customer->setActiveCampaignId(18)->shouldBeCalledOnce();
        $entityManager->flush()->shouldBeCalledOnce();
        $messageBus->dispatch(Argument::type(ContactUpdate::class))->shouldBeCalledOnce()->willReturn(new Envelope(new ContactUpdate(1, 18)));

        $this->enqueue($customer);
    }

    public function it_enqueues_a_contact_create_if_both_customer_contact_active_campaign_id_is_null_and_a_contact_with_the_same_email_does_not_exist(
        CustomerInterface $customer,
        MessageBusInterface $messageBus,
        EntityManagerInterface $entityManager
    ): void {
        $customer->setActiveCampaignId(Argument::any())->shouldNotBeCalled();
        $entityManager->flush()->shouldNotBeCalled();
        $messageBus->dispatch(Argument::type(ContactCreate::class))->shouldBeCalledOnce()->willReturn(new Envelope(new ContactCreate(1)));

        $this->enqueue($customer);
    }
}
