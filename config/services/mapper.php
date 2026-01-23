<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactMapper;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ConnectionMapper;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceCustomerMapper;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderDiscountMapper;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderMapper;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\EcommerceOrderProductMapper;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\TagMapper;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\ContactListMapper;
use Webgriffe\SyliusActiveCampaignPlugin\Mapper\WebhookMapper;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('webgriffe.sylius_active_campaign_plugin.mapper.contact', ContactMapper::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.factory.active_campaign.contact'),
            service('event_dispatcher'),
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.mapper.connection', ConnectionMapper::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.factory.active_campaign.connection'),
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.mapper.ecommerce_customer', EcommerceCustomerMapper::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.factory.active_campaign.ecommerce_customer'),
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.mapper.ecommerce_order', EcommerceOrderMapper::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.factory.active_campaign.ecommerce_order'),
            service('webgriffe.sylius_active_campaign_plugin.mapper.ecommerce_order_product'),
            service('webgriffe.sylius_active_campaign_plugin.mapper.ecommerce_order_discount'),
            service('webgriffe.sylius_active_campaign_plugin.generator.channel_hostname_url'),
            '%kernel.default_locale%',
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.mapper.ecommerce_order_product', EcommerceOrderProductMapper::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.factory.active_campaign.ecommerce_order_product'),
            service('webgriffe.sylius_active_campaign_plugin.generator.channel_hostname_url'),
            '%kernel.default_locale%',
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.mapper.ecommerce_order_discount', EcommerceOrderDiscountMapper::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.factory.active_campaign.ecommerce_order_discount'),
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.mapper.tag', TagMapper::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.factory.active_campaign.tag'),
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.mapper.contact_list', ContactListMapper::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.factory.active_campaign.contact_list'),
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.mapper.webhook', WebhookMapper::class)
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.factory.active_campaign.webhook'),
        ])
    ;
};
