<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Webgriffe\SyliusActiveCampaignPlugin\EventSubscriber\CustomerSubscriber;
use Webgriffe\SyliusActiveCampaignPlugin\EventSubscriber\ChannelSubscriber;
use Webgriffe\SyliusActiveCampaignPlugin\EventSubscriber\OrderSubscriber;
use Webgriffe\SyliusActiveCampaignPlugin\EventSubscriber\OrderPaymentWorkflowSubscriber;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('webgriffe.sylius_active_campaign_plugin.event_subscriber.customer', CustomerSubscriber::class)
        ->args([
            service('webgriffe_sylius_active_campaign_plugin.command_bus'),
            service('webgriffe.sylius_active_campaign_plugin.resolver.enqueuable_channels'),
            service('webgriffe.sylius_active_campaign_plugin.enqueuer.contact'),
            service('webgriffe.sylius_active_campaign_plugin.enqueuer.ecommerce_customer'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
        ])
        ->tag('kernel.event_subscriber')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.event_subscriber.channel', ChannelSubscriber::class)
        ->args([
            service('webgriffe_sylius_active_campaign_plugin.command_bus'),
            service('webgriffe.sylius_active_campaign_plugin.enqueuer.connection'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
        ])
        ->tag('kernel.event_subscriber')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.event_subscriber.order', OrderSubscriber::class)
        ->args([
            service('webgriffe_sylius_active_campaign_plugin.command_bus'),
            service('webgriffe.sylius_active_campaign_plugin.enqueuer.ecommerce_order'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
            service('webgriffe.sylius_active_campaign_plugin.enqueuer.contact'),
            service('webgriffe.sylius_active_campaign_plugin.enqueuer.ecommerce_customer'),
            service('webgriffe.sylius_active_campaign_plugin.resolver.enqueuable_channels'),
        ])
        ->tag('kernel.event_subscriber')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.event_subscriber.order_payment_workflow', OrderPaymentWorkflowSubscriber::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.enqueuer.real_time_order'),
        ])
        ->tag('kernel.event_subscriber')
    ;
};
