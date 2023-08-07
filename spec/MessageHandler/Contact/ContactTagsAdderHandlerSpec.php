<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact;

use Tests\Webgriffe\SyliusActiveCampaignPlugin\App\Entity\Customer\CustomerInterface;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\CustomerInterface as SyliusCustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ContactTagFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\TagMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactTagsAdder;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact\ContactTagsAdderHandler;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactTagInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\TagInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\ContactTagsResolverInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\CreateResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Tag\TagResponse;

class ContactTagsAdderHandlerSpec extends ObjectBehavior
{
    public function let(
        ActiveCampaignResourceClientInterface $activeCampaignTagClient,
        ActiveCampaignResourceClientInterface $activeCampaignContactTagClient,
        CustomerRepositoryInterface $customerRepository,
        ContactTagsResolverInterface $contactTagsResolver,
        TagMapperInterface $tagMapper,
        ContactTagFactoryInterface $contactTagFactory,
        LoggerInterface $logger,
        CustomerInterface $customer,
        ListResourcesResponseInterface $maleResourcesResponse,
        ResourceResponseInterface $maleTagResponse,
        ListResourcesResponseInterface $newResourcesResponse,
        TagInterface $newTag,
        CreateResourceResponseInterface $createResourceResponse,
        ResourceResponseInterface $newTagResponse,
        ContactTagInterface $maleContactTag,
        ContactTagInterface $newContactTag,
        CreateResourceResponseInterface $createMaleContactTagResourceResponse
    ): void {
        $customerRepository->find(12)->willReturn($customer);
        $customer->getActiveCampaignId()->willReturn(143);

        $contactTagsResolver->resolve($customer)->willReturn(['male', 'new']);

        $maleTagResponse->getId()->willReturn(4);
        $maleResourcesResponse->getResourceResponseLists()->willReturn([$maleTagResponse]);
        $activeCampaignTagClient->list(['search' => 'male'])->willReturn($maleResourcesResponse);

        $newResourcesResponse->getResourceResponseLists()->willReturn([]);
        $activeCampaignTagClient->list(['search' => 'new'])->willReturn($newResourcesResponse);

        $tagMapper->mapFromTagAndTagType('new')->willReturn($newTag);

        $newTagResponse->getId()->willReturn(7);
        $createResourceResponse->getResourceResponse()->willReturn($newTagResponse);
        $activeCampaignTagClient->create($newTag)->willReturn($createResourceResponse);

        $contactTagFactory->createNew(143, 4)->willReturn($maleContactTag);
        $contactTagFactory->createNew(143, 7)->willReturn($newContactTag);

        $activeCampaignContactTagClient->create($maleContactTag)->willReturn($createMaleContactTagResourceResponse);
        $activeCampaignContactTagClient->create($newContactTag)->willThrow(new HttpException(200));

        $this->beConstructedWith($activeCampaignTagClient, $activeCampaignContactTagClient, $customerRepository, $contactTagsResolver, $tagMapper, $contactTagFactory, $logger);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ContactTagsAdderHandler::class);
    }

    public function it_throws_if_customer_is_not_found(
        CustomerRepositoryInterface $customerRepository
    ): void {
        $customerRepository->find(12)->shouldBeCalledOnce()->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('Customer with id "12" does not exists.'))->during(
            '__invoke',
            [new ContactTagsAdder(12)]
        );
    }

    public function it_throws_if_customer_is_not_an_implementation_of_active_campaign_aware_interface(
        CustomerRepositoryInterface $customerRepository,
        SyliusCustomerInterface $syliusCustomer
    ): void {
        $customerRepository->find(12)->shouldBeCalledOnce()->willReturn($syliusCustomer);

        $this->shouldThrow(new InvalidArgumentException('The Customer entity should implement the "Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface" class.'))->during(
            '__invoke',
            [new ContactTagsAdder(12)]
        );
    }

    public function it_throws_if_customer_has_not_been_exported_to_active_campaign_yet(
        ActiveCampaignAwareInterface $customer
    ): void {
        $customer->getActiveCampaignId()->willReturn(null);

        $this->shouldThrow(new InvalidArgumentException('The Customer with id "12" does not have an ActiveCampaign id.'))->during(
            '__invoke',
            [new ContactTagsAdder(12)]
        );
    }

    public function it_throws_if_adding_customer_tag_throws_not_200_or_201_response(
        ContactTagFactoryInterface $contactTagFactory
    ): void {
        $contactTagFactory->createNew(143, 7)->willThrow(new HttpException(205));

        $this->shouldThrow(new HttpException(205))->during(
            '__invoke',
            [new ContactTagsAdder(12)]
        );
    }

    public function it_adds_contact_tags(
        LoggerInterface $logger
    ): void {
        $logger->info('The tag with id "7" already exists for the contact with id "143".')->shouldBeCalledOnce();

        $this->__invoke(new ContactTagsAdder(12));
    }
}
