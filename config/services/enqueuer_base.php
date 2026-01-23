<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\ContactEnqueuer;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\ConnectionEnqueuer;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\EcommerceCustomerEnqueuer;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\EcommerceOrderEnqueuer;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\WebhookEnqueuer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('webgriffe.sylius_active_campaign_plugin.enqueuer.contact', ContactEnqueuer::class)
        ->args([
            service('webgriffe_sylius_active_campaign_plugin.command_bus'),
            service('webgriffe_sylius_active_campaign_plugin.client.active_campaign.contact'),
            service('doctrine.orm.entity_manager'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.enqueuer.connection', ConnectionEnqueuer::class)
        ->args([
            service('webgriffe_sylius_active_campaign_plugin.command_bus'),
            service('webgriffe_sylius_active_campaign_plugin.client.active_campaign.connection'),
            service('doctrine.orm.entity_manager'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.enqueuer.ecommerce_customer', EcommerceCustomerEnqueuer::class)
        ->args([
            service('webgriffe_sylius_active_campaign_plugin.command_bus'),
            service('webgriffe.sylius_active_campaign_plugin.client.active_campaign.ecommerce_customer'),
            service('doctrine.orm.entity_manager'),
            service('webgriffe_sylius_active_campaign.factory.channel_customer'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.enqueuer.ecommerce_order', EcommerceOrderEnqueuer::class)
        ->args([
            service('webgriffe_sylius_active_campaign_plugin.command_bus'),
            service('doctrine.orm.entity_manager'),
            service('webgriffe.sylius_active_campaign_plugin.client.active_campaign.ecommerce_order'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.enqueuer.webhook', WebhookEnqueuer::class)
        ->args([
            service('webgriffe_sylius_active_campaign_plugin.command_bus'),
            service('webgriffe.sylius_active_campaign_plugin.client.active_campaign.webhook'),
            service('webgriffe_sylius_active_campaign_plugin.generator.channel_hostname_url'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
        ])
    ;
};
