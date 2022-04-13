<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ContactTagFactoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\TagMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactTagsAdder;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\ContactTagsResolverInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Tag\TagResponse;

final class ContactTagsAdderHandler
{
    public function __construct(
        private ActiveCampaignResourceClientInterface $activeCampaignTagClient,
        private ActiveCampaignResourceClientInterface $activeCampaignContactTagClient,
        private CustomerRepositoryInterface $customerRepository,
        private ContactTagsResolverInterface $contactTagsResolver,
        private TagMapperInterface $tagMapper,
        private ContactTagFactoryInterface $contactTagFactory,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(ContactTagsAdder $message): void
    {
        $customerId = $message->getCustomerId();
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->find($customerId);
        if ($customer === null) {
            throw new InvalidArgumentException(sprintf('Customer with id "%s" does not exists.', $customerId));
        }
        if (!$customer instanceof ActiveCampaignAwareInterface) {
            throw new InvalidArgumentException(sprintf('The Customer entity should implement the "%s" class.', ActiveCampaignAwareInterface::class));
        }
        $activeCampaignContactId = $customer->getActiveCampaignId();
        if ($activeCampaignContactId === null) {
            throw new InvalidArgumentException(sprintf('The Customer with id "%s" does not have an ActiveCampaign id.', $customerId));
        }
        $tags = $this->contactTagsResolver->resolve($customer);
        /** @var int[] $activeCampaignTagIds */
        $activeCampaignTagIds = [];
        foreach ($tags as $tag) {
            $activeCampaignTagIds[] = $this->retrieveActiveCampaignTagId($tag);
        }

        foreach ($activeCampaignTagIds as $activeCampaignTagId) {
            try {
                $this->activeCampaignContactTagClient->create($this->contactTagFactory->createNew($activeCampaignContactId, $activeCampaignTagId));
            } catch (HttpException $httpException) {
                if ($httpException->getStatusCode() !== 200) {
                    throw $httpException;
                }
                $this->logger->info(sprintf('The tag with id "%s" already exists for the contact with id "%s".', $activeCampaignTagId, $activeCampaignContactId));

                continue;
            }
        }
    }

    private function retrieveActiveCampaignTagId(string $tag): int
    {
        $listActiveCampaignTags = $this->activeCampaignTagClient->list(['search' => $tag])->getResourceResponseLists();
        if (count($listActiveCampaignTags) > 0) {
            /** @var TagResponse $activeCampaignTag */
            $activeCampaignTag = reset($listActiveCampaignTags);

            return $activeCampaignTag->getId();
        }
        $mappedTag = $this->tagMapper->mapFromTagAndTagType($tag);
        $createActiveCampaignTagResponse = $this->activeCampaignTagClient->create($mappedTag);
        /** @var TagResponse $activeCampaignTag */
        $activeCampaignTag = $createActiveCampaignTagResponse->getResourceResponse();

        return $activeCampaignTag->getId();
    }
}
