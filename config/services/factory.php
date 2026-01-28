<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ContactFactory;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ConnectionFactory;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceCustomerFactory;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderDiscountFactory;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderFactory;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderProductFactory;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\TagFactory;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ContactTagFactory;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ContactListFactory;
use Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\WebhookFactory;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Contact;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Connection;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceCustomer;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrder;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderDiscount;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\EcommerceOrderProduct;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Tag;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactTag;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\ContactList;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\Webhook;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('webgriffe.sylius_active_campaign_plugin.factory.active_campaign.contact', ContactFactory::class)
        ->args([
            Contact::class,
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.factory.active_campaign.connection', ConnectionFactory::class)
        ->args([
            Connection::class,
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.factory.active_campaign.ecommerce_customer', EcommerceCustomerFactory::class)
        ->args([
            EcommerceCustomer::class,
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.factory.active_campaign.ecommerce_order', EcommerceOrderFactory::class)
        ->args([
            EcommerceOrder::class,
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.factory.active_campaign.ecommerce_order_product', EcommerceOrderProductFactory::class)
        ->args([
            EcommerceOrderProduct::class,
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.factory.active_campaign.ecommerce_order_discount', EcommerceOrderDiscountFactory::class)
        ->args([
            EcommerceOrderDiscount::class,
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.factory.active_campaign.tag', TagFactory::class)
        ->args([
            Tag::class,
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.factory.active_campaign.contact_tag', ContactTagFactory::class)
        ->args([
            ContactTag::class,
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.factory.active_campaign.contact_list', ContactListFactory::class)
        ->args([
            ContactList::class,
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.factory.active_campaign.webhook', WebhookFactory::class)
        ->args([
            Webhook::class,
        ])
    ;
};
