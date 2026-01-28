<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Webgriffe\SyliusActiveCampaignPlugin\Command\EnqueueContactAndEcommerceCustomerCommand;
use Webgriffe\SyliusActiveCampaignPlugin\Command\EnqueueConnectionCommand;
use Webgriffe\SyliusActiveCampaignPlugin\Command\EnqueueEcommerceOrderCommand;
use Webgriffe\SyliusActiveCampaignPlugin\Command\EnqueueEcommerceAbandonedCartCommand;
use Webgriffe\SyliusActiveCampaignPlugin\Command\EnqueueWebhookCommand;
use Webgriffe\SyliusActiveCampaignPlugin\Command\EnqueueContactListsSubscriptionCommand;
use Webgriffe\SyliusActiveCampaignPlugin\Command\UpdateContactListsSubscriptionCommand;
use Webgriffe\SyliusActiveCampaignPlugin\Command\EnqueueContactTagsAdderCommand;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $services = $containerConfigurator->services();

    $parameters
        ->set('webgriffe_sylius_active_campaign.command.enqueue_contact_and_ecommerce_customer.name', 'webgriffe:active-campaign:enqueue-contact-and-ecommerce-customer')
        ->set('webgriffe_sylius_active_campaign.command.enqueue_connection.name', 'webgriffe:active-campaign:enqueue-connection')
        ->set('webgriffe_sylius_active_campaign.command.enqueue_ecommerce_order.name', 'webgriffe:active-campaign:enqueue-ecommerce-order')
        ->set('webgriffe_sylius_active_campaign.command.enqueue_ecommerce_abandoned_cart.name', 'webgriffe:active-campaign:enqueue-ecommerce-abandoned-cart')
        ->set('webgriffe_sylius_active_campaign.command.enqueue_webhook.name', 'webgriffe:active-campaign:enqueue-webhook')
        ->set('webgriffe_sylius_active_campaign.command.enqueue_contact_lists_subscription.name', 'webgriffe:active-campaign:enqueue-contact-lists-subscription')
        ->set('webgriffe_sylius_active_campaign.command.update_contact_lists_subscription.name', 'webgriffe:active-campaign:update-contact-lists-subscription')
        ->set('webgriffe_sylius_active_campaign.command.enqueue_contact_tags_adder.name', 'webgriffe:active-campaign:enqueue-contact-tags-adder')
        ->set('webgriffe_sylius_active_campaign.cart_becomes_abandoned_period', '1 day')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.command.enqueue_contact_and_ecommerce_customer', EnqueueContactAndEcommerceCustomerCommand::class)
        ->args([
            service('sylius.repository.customer'),
            service('webgriffe.sylius_active_campaign_plugin.resolver.enqueuable_channels'),
            service('webgriffe.sylius_active_campaign_plugin.enqueuer.contact'),
            service('webgriffe.sylius_active_campaign_plugin.enqueuer.ecommerce_customer'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
            '%webgriffe_sylius_active_campaign.command.enqueue_contact_and_ecommerce_customer.name%',
        ])
        ->tag('console.command')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.command.enqueue_connection', EnqueueConnectionCommand::class)
        ->args([
            service('sylius.repository.channel'),
            service('webgriffe.sylius_active_campaign_plugin.enqueuer.connection'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
            '%webgriffe_sylius_active_campaign.command.enqueue_connection.name%',
        ])
        ->tag('console.command')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.command.enqueue_ecommerce_order', EnqueueEcommerceOrderCommand::class)
        ->args([
            service('sylius.repository.order'),
            service('webgriffe.sylius_active_campaign_plugin.enqueuer.ecommerce_order'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
            '%webgriffe_sylius_active_campaign.command.enqueue_ecommerce_order.name%',
        ])
        ->tag('console.command')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.command.enqueue_ecommerce_abandoned_cart', EnqueueEcommerceAbandonedCartCommand::class)
        ->args([
            service('sylius.repository.order'),
            service('webgriffe.sylius_active_campaign_plugin.enqueuer.ecommerce_order'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
            service('webgriffe.sylius_active_campaign_plugin.enqueuer.contact'),
            service('webgriffe.sylius_active_campaign_plugin.enqueuer.ecommerce_customer'),
            service('webgriffe_sylius_active_campaign_plugin.command_bus'),
            '%webgriffe_sylius_active_campaign.cart_becomes_abandoned_period%',
            '%webgriffe_sylius_active_campaign.command.enqueue_ecommerce_abandoned_cart.name%',
        ])
        ->tag('console.command')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.command.enqueue_webhook', EnqueueWebhookCommand::class)
        ->args([
            service('sylius.repository.channel'),
            service('webgriffe.sylius_active_campaign_plugin.enqueuer.webhook'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
            '%webgriffe_sylius_active_campaign.command.enqueue_webhook.name%',
        ])
        ->tag('console.command')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.command.enqueue_contact_lists_subscription', EnqueueContactListsSubscriptionCommand::class)
        ->args([
            service('sylius.repository.customer'),
            service('webgriffe_sylius_active_campaign_plugin.command_bus'),
            '%webgriffe_sylius_active_campaign.command.enqueue_contact_lists_subscription.name%',
        ])
        ->tag('console.command')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.command.update_contact_lists_subscription', UpdateContactListsSubscriptionCommand::class)
        ->args([
            service('sylius.repository.customer'),
            service('webgriffe_sylius_active_campaign_plugin.command_bus'),
            '%webgriffe_sylius_active_campaign.command.update_contact_lists_subscription.name%',
        ])
        ->tag('console.command')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.command.enqueue_contact_tags_adder', EnqueueContactTagsAdderCommand::class)
        ->args([
            service('sylius.repository.customer'),
            service('webgriffe_sylius_active_campaign_plugin.command_bus'),
            '%webgriffe_sylius_active_campaign.command.enqueue_contact_tags_adder.name%',
        ])
        ->tag('console.command')
    ;
};
