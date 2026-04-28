<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact\ContactCreateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact\ContactUpdateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact\ContactRemoveHandler;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact\ContactTagsAdderHandler;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact\ContactListsSubscriberHandler;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact\ContactListsUpdaterHandler;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Connection\ConnectionCreateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Connection\ConnectionUpdateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Connection\ConnectionRemoveHandler;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer\EcommerceCustomerCreateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer\EcommerceCustomerUpdateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceCustomer\EcommerceCustomerRemoveHandler;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceOrder\EcommerceOrderCreateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceOrder\EcommerceOrderUpdateHandler;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\EcommerceOrder\EcommerceOrderRemoveHandler;
use Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Webhook\WebhookCreateHandler;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('webgriffe.sylius_active_campaign_plugin.message_handler.contact.create', ContactCreateHandler::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.mapper.contact'),
            service('webgriffe.sylius_active_campaign_plugin.client.active_campaign.contact'),
            service('sylius.repository.customer'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
            service('webgriffe_sylius_active_campaign_plugin.command_bus'),
        ])
        ->tag('messenger.message_handler')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.message_handler.contact.update', ContactUpdateHandler::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.mapper.contact'),
            service('webgriffe.sylius_active_campaign_plugin.client.active_campaign.contact'),
            service('sylius.repository.customer'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
        ])
        ->tag('messenger.message_handler')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.message_handler.contact.remove', ContactRemoveHandler::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.client.active_campaign.contact'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
        ])
        ->tag('messenger.message_handler')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.message_handler.contact.tags_adder', ContactTagsAdderHandler::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.client.active_campaign.tag'),
            service('webgriffe.sylius_active_campaign_plugin.client.active_campaign.contact_tag'),
            service('sylius.repository.customer'),
            service('webgriffe.sylius_active_campaign_plugin.resolver.contact_tags'),
            service('webgriffe.sylius_active_campaign_plugin.mapper.tag'),
            service('webgriffe.sylius_active_campaign_plugin.factory.active_campaign.contact_tag'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
        ])
        ->tag('messenger.message_handler')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.message_handler.contact.lists_subscriber', ContactListsSubscriberHandler::class)
        ->args([
            service('sylius.repository.customer'),
            service('webgriffe.sylius_active_campaign_plugin.resolver.enqueuable_channels'),
            service('webgriffe.sylius_active_campaign_plugin.resolver.channel_customer_based_list_subscription_status'),
            service('webgriffe.sylius_active_campaign_plugin.client.active_campaign.contact_list'),
            service('webgriffe.sylius_active_campaign_plugin.mapper.contact_list'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
        ])
        ->tag('messenger.message_handler')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.message_handler.contact.lists_updater', ContactListsUpdaterHandler::class)
        ->args([
            service('sylius.repository.customer'),
            service('webgriffe.sylius_active_campaign_plugin.resolver.enqueuable_channels'),
            service('webgriffe.sylius_active_campaign_plugin.client.active_campaign.contact'),
            service('webgriffe.sylius_active_campaign_plugin.updater.channel_customer_based_list_subscription_status'),
        ])
        ->tag('messenger.message_handler')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.message_handler.connection.create', ConnectionCreateHandler::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.mapper.connection'),
            service('webgriffe.sylius_active_campaign_plugin.client.active_campaign.connection'),
            service('sylius.repository.channel'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
            service('webgriffe_sylius_active_campaign_plugin.command_bus'),
        ])
        ->tag('messenger.message_handler')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.message_handler.connection.update', ConnectionUpdateHandler::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.mapper.connection'),
            service('webgriffe.sylius_active_campaign_plugin.client.active_campaign.connection'),
            service('sylius.repository.channel'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
        ])
        ->tag('messenger.message_handler')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.message_handler.connection.remove', ConnectionRemoveHandler::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.client.active_campaign.connection'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
        ])
        ->tag('messenger.message_handler')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.message_handler.ecommerce_customer.create', EcommerceCustomerCreateHandler::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.mapper.ecommerce_customer'),
            service('webgriffe.sylius_active_campaign_plugin.client.active_campaign.ecommerce_customer'),
            service('sylius.repository.customer'),
            service('sylius.repository.channel'),
            service('webgriffe_sylius_active_campaign.factory.channel_customer'),
            service('doctrine.orm.entity_manager'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
            service('webgriffe_sylius_active_campaign_plugin.command_bus'),
        ])
        ->tag('messenger.message_handler')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.message_handler.ecommerce_customer.update', EcommerceCustomerUpdateHandler::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.mapper.ecommerce_customer'),
            service('webgriffe.sylius_active_campaign_plugin.client.active_campaign.ecommerce_customer'),
            service('sylius.repository.customer'),
            service('sylius.repository.channel'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
        ])
        ->tag('messenger.message_handler')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.message_handler.ecommerce_customer.remove', EcommerceCustomerRemoveHandler::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.client.active_campaign.ecommerce_customer'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
        ])
        ->tag('messenger.message_handler')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.message_handler.ecommerce_order.create', EcommerceOrderCreateHandler::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.mapper.ecommerce_order'),
            service('webgriffe.sylius_active_campaign_plugin.client.active_campaign.ecommerce_order'),
            service('sylius.repository.order'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
            service('webgriffe_sylius_active_campaign_plugin.command_bus'),
        ])
        ->tag('messenger.message_handler')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.message_handler.ecommerce_order.update', EcommerceOrderUpdateHandler::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.mapper.ecommerce_order'),
            service('webgriffe.sylius_active_campaign_plugin.client.active_campaign.ecommerce_order'),
            service('sylius.repository.order'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
        ])
        ->tag('messenger.message_handler')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.message_handler.ecommerce_order.remove', EcommerceOrderRemoveHandler::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.client.active_campaign.ecommerce_order'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
        ])
        ->tag('messenger.message_handler')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.message_handler.webhook.create', WebhookCreateHandler::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.mapper.webhook'),
            service('webgriffe.sylius_active_campaign_plugin.client.active_campaign.webhook'),
            service('sylius.repository.channel'),
            service('webgriffe.sylius_active_campaign_plugin.generator.channel_hostname_url'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
        ])
        ->tag('messenger.message_handler')
    ;
};
