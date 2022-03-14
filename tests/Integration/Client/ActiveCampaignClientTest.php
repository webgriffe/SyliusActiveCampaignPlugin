<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusActiveCampaignPlugin\Integration\Client;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\HttpClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignClientInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Contact;

final class ActiveCampaignClientTest extends KernelTestCase
{
    private ActiveCampaignClientInterface $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = self::getContainer()->get('webgriffe.sylius_active_campaign_plugin.client.active_campaign');
    }

    public function test_it_should_create_contact_on_active_campaign(): void
    {
        HttpClientStub::$responseBodyContent = '{"fieldValues":[],"email":"test@email.com","cdate":"2022-03-07T10:16:24-06:00","udate":"2022-03-07T10:16:24-06:00","origid":"ABC123","organization":"Webgriffe SRL","links":[],"id":"1"}';
        $contact = new Contact('test@email.com', 'John', 'Wayne', 123456789, ['street' => 'Via Canale 1/P']);

        $createdContact = $this->service->createContact($contact);

        self::assertNotNull($createdContact);
    }
}
