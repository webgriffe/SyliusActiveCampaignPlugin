<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClient;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\ContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\CreateContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\RetrieveContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\ListContactsResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Contact\UpdateContactResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection\ConnectionResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection\CreateConnectionResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection\ListConnectionsResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Connection\UpdateConnectionResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer\EcommerceCustomerResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer\CreateEcommerceCustomerResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer\ListEcommerceCustomersResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceCustomer\UpdateEcommerceCustomerResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder\EcommerceOrderResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder\CreateEcommerceOrderResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder\ListEcommerceOrdersResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\EcommerceOrder\UpdateEcommerceOrderResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Tag\TagResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Tag\CreateTagResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Tag\ListTagsResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Tag\UpdateTagResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ContactTag\ContactTagResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ContactTag\CreateContactTagResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ContactList\ContactListResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ContactList\CreateContactListResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Webhook\WebhookResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Webhook\CreateWebhookResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Webhook\ListWebhooksResponse;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\Webhook\UpdateWebhookResponse;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('webgriffe.sylius_active_campaign_plugin.client.active_campaign.contact', ActiveCampaignResourceClient::class)
        ->arg('$httpClient', service('webgriffe.sylius_active_campaign_plugin.http_client.active_campaign'))
        ->arg('$serializer', service('serializer'))
        ->arg('$resourceName', 'contact')
        ->arg('$resourceResponseType', ContactResponse::class)
        ->arg('$createResourceResponseType', CreateContactResponse::class)
        ->arg('$retrieveResourceResponseType', RetrieveContactResponse::class)
        ->arg('$listResourcesResponseType', ListContactsResponse::class)
        ->arg('$updateResourceResponseType', UpdateContactResponse::class)
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.client.active_campaign.connection', ActiveCampaignResourceClient::class)
        ->arg('$httpClient', service('webgriffe.sylius_active_campaign_plugin.http_client.active_campaign'))
        ->arg('$serializer', service('serializer'))
        ->arg('$resourceName', 'connection')
        ->arg('$resourceResponseType', ConnectionResponse::class)
        ->arg('$createResourceResponseType', CreateConnectionResponse::class)
        ->arg('$retrieveResourceResponseType', null)
        ->arg('$listResourcesResponseType', ListConnectionsResponse::class)
        ->arg('$updateResourceResponseType', UpdateConnectionResponse::class)
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.client.active_campaign.ecommerce_customer', ActiveCampaignResourceClient::class)
        ->arg('$httpClient', service('webgriffe.sylius_active_campaign_plugin.http_client.active_campaign'))
        ->arg('$serializer', service('serializer'))
        ->arg('$resourceName', 'ecomCustomer')
        ->arg('$resourceResponseType', EcommerceCustomerResponse::class)
        ->arg('$createResourceResponseType', CreateEcommerceCustomerResponse::class)
        ->arg('$retrieveResourceResponseType', null)
        ->arg('$listResourcesResponseType', ListEcommerceCustomersResponse::class)
        ->arg('$updateResourceResponseType', UpdateEcommerceCustomerResponse::class)
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.client.active_campaign.ecommerce_order', ActiveCampaignResourceClient::class)
        ->arg('$httpClient', service('webgriffe.sylius_active_campaign_plugin.http_client.active_campaign'))
        ->arg('$serializer', service('serializer'))
        ->arg('$resourceName', 'ecomOrder')
        ->arg('$resourceResponseType', EcommerceOrderResponse::class)
        ->arg('$createResourceResponseType', CreateEcommerceOrderResponse::class)
        ->arg('$retrieveResourceResponseType', null)
        ->arg('$listResourcesResponseType', ListEcommerceOrdersResponse::class)
        ->arg('$updateResourceResponseType', UpdateEcommerceOrderResponse::class)
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.client.active_campaign.tag', ActiveCampaignResourceClient::class)
        ->arg('$httpClient', service('webgriffe.sylius_active_campaign_plugin.http_client.active_campaign'))
        ->arg('$serializer', service('serializer'))
        ->arg('$resourceName', 'tag')
        ->arg('$resourceResponseType', TagResponse::class)
        ->arg('$createResourceResponseType', CreateTagResponse::class)
        ->arg('$retrieveResourceResponseType', null)
        ->arg('$listResourcesResponseType', ListTagsResponse::class)
        ->arg('$updateResourceResponseType', UpdateTagResponse::class)
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.client.active_campaign.contact_tag', ActiveCampaignResourceClient::class)
        ->arg('$httpClient', service('webgriffe.sylius_active_campaign_plugin.http_client.active_campaign'))
        ->arg('$serializer', service('serializer'))
        ->arg('$resourceName', 'contactTag')
        ->arg('$resourceResponseType', ContactTagResponse::class)
        ->arg('$createResourceResponseType', CreateContactTagResponse::class)
        ->arg('$retrieveResourceResponseType', null)
        ->arg('$listResourcesResponseType', null)
        ->arg('$updateResourceResponseType', null)
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.client.active_campaign.contact_list', ActiveCampaignResourceClient::class)
        ->arg('$httpClient', service('webgriffe.sylius_active_campaign_plugin.http_client.active_campaign'))
        ->arg('$serializer', service('serializer'))
        ->arg('$resourceName', 'contactList')
        ->arg('$resourceResponseType', ContactListResponse::class)
        ->arg('$createResourceResponseType', CreateContactListResponse::class)
        ->arg('$retrieveResourceResponseType', null)
        ->arg('$listResourcesResponseType', null)
        ->arg('$updateResourceResponseType', null)
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.client.active_campaign.webhook', ActiveCampaignResourceClient::class)
        ->arg('$httpClient', service('webgriffe.sylius_active_campaign_plugin.http_client.active_campaign'))
        ->arg('$serializer', service('serializer'))
        ->arg('$resourceName', 'webhook')
        ->arg('$resourceResponseType', WebhookResponse::class)
        ->arg('$createResourceResponseType', CreateWebhookResponse::class)
        ->arg('$retrieveResourceResponseType', null)
        ->arg('$listResourcesResponseType', ListWebhooksResponse::class)
        ->arg('$updateResourceResponseType', UpdateWebhookResponse::class)
    ;
};
