<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactMapperInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactCreate;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\ContactResponse;

final class ContactCreateHandler
{
    public function __construct(
        private ContactMapperInterface $contactMapper,
        private ActiveCampaignResourceClientInterface $activeCampaignContactClient,
        private CustomerRepositoryInterface $customerRepository,
        private ?LoggerInterface $logger = null,
    ) {
        if ($this->logger === null) {
            trigger_deprecation(
                'webgriffe/sylius-active-campaign-plugin',
                'v0.12.2',
                'The logger argument is mandatory.',
            );
        }
    }

    /**
     * @throws \Throwable
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function __invoke(ContactCreate $message): void
    {
        $customerId = $message->getCustomerId();
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->find($customerId);
        if ($customer === null) {
            throw new InvalidArgumentException(sprintf('Customer with id "%s" does not exists', $customerId));
        }
        if (!$customer instanceof ActiveCampaignAwareInterface) {
            throw new InvalidArgumentException(sprintf('The Customer entity should implement the "%s" class', ActiveCampaignAwareInterface::class));
        }

        $activeCampaignId = $customer->getActiveCampaignId();
        if ($activeCampaignId !== null) {
            throw new InvalidArgumentException(sprintf('The Customer with id "%s" has been already created on ActiveCampaign on the contact with id "%s"', $customerId, $activeCampaignId));
        }

        try {
            $createContactResponse = $this->activeCampaignContactClient->create($this->contactMapper->mapFromCustomer($customer));
            $activeCampaignContactId = $createContactResponse->getResourceResponse()->getId();
        } catch (UnprocessableEntityHttpException $e) {
            // If validation fails try to check if contact already exists
            $searchContactsForEmail = $this->activeCampaignContactClient->list(['email' => (string) $customer->getEmail()])->getResourceResponseLists();
            if (count($searchContactsForEmail) < 1) {
                throw $e;
            }
            /** @var ContactResponse $contact */
            $contact = reset($searchContactsForEmail);
            $activeCampaignContactId = $contact->getId();
            $this->logger?->warning(sprintf(
                'Contact with email "%s" already exists on ActiveCampaign with id "%s". Why it has not been found before?',
                (string) $customer->getEmail(),
                $activeCampaignContactId,
            ));
        } catch (\Throwable $e) {
            $this->logger?->error($e->getMessage(), $e->getTrace());

            throw $e;
        }
        $customer->setActiveCampaignId($activeCampaignContactId);
        $this->customerRepository->add($customer);
    }
}
