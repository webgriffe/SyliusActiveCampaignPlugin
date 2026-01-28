<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\ActiveCampaignConnectionClientStub;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\ActiveCampaignContactClientStub;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\ActiveCampaignEcommerceCustomerClientStub;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\ActiveCampaignEcommerceOrderClientStub;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\ActiveCampaignWebhookClientStub;
use Tests\Webgriffe\SyliusActiveCampaignPlugin\Stub\HttpClientStub;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\ConnectionEnqueuer;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\ContactEnqueuer;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\EcommerceCustomerEnqueuer;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\EcommerceOrderEnqueuer;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\WebhookEnqueuer;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $container) {
    if (str_starts_with($container->env(), 'test')) {
        $container->import('../../../vendor/sylius/sylius/src/Sylius/Behat/Resources/config/services.xml');
    }

    $services = $container->services();

    // workaround needed for strange "test.client.history" problem
    // see https://github.com/FriendsOfBehat/SymfonyExtension/issues/88
    $services->set('webgriffe.sylius_active_campaign_plugin.http_client.active_campaign', HttpClientStub::class);

    $services->set('webgriffe.sylius_active_campaign_plugin.client_stub.active_campaign.contact', ActiveCampaignContactClientStub::class);

    $services->set('webgriffe.sylius_active_campaign_plugin.client_stub.active_campaign.connection', ActiveCampaignConnectionClientStub::class);

    $services->set('webgriffe.sylius_active_campaign_plugin.client_stub.active_campaign.ecommerce_customer', ActiveCampaignEcommerceCustomerClientStub::class);

    $services->set('webgriffe.sylius_active_campaign_plugin.client_stub.active_campaign.ecommerce_order', ActiveCampaignEcommerceOrderClientStub::class);

    $services->set('webgriffe.sylius_active_campaign_plugin.client_stub.active_campaign.webhook', ActiveCampaignWebhookClientStub::class);

    $services->set('webgriffe.sylius_active_campaign_plugin.enqueuer.contact', ContactEnqueuer::class)
        ->args([
            service('messenger.default_bus'),
            service('webgriffe.sylius_active_campaign_plugin.client_stub.active_campaign.contact'),
            service('doctrine.orm.entity_manager'),
        ]);

    $services->set('webgriffe.sylius_active_campaign_plugin.enqueuer.connection', ConnectionEnqueuer::class)
        ->args([
            service('messenger.default_bus'),
            service('webgriffe.sylius_active_campaign_plugin.client_stub.active_campaign.connection'),
            service('doctrine.orm.entity_manager'),
        ]);

    $services->set('webgriffe.sylius_active_campaign_plugin.enqueuer.ecommerce_customer', EcommerceCustomerEnqueuer::class)
        ->args([
            service('messenger.default_bus'),
            service('webgriffe.sylius_active_campaign_plugin.client_stub.active_campaign.ecommerce_customer'),
            service('doctrine.orm.entity_manager'),
            service('webgriffe_sylius_active_campaign.factory.channel_customer'),
        ]);

    $services->set('webgriffe.sylius_active_campaign_plugin.enqueuer.ecommerce_order', EcommerceOrderEnqueuer::class)
        ->args([
            service('messenger.default_bus'),
            service('doctrine.orm.entity_manager'),
            service('webgriffe.sylius_active_campaign_plugin.client_stub.active_campaign.ecommerce_order'),
        ]);

    $services->set('webgriffe.sylius_active_campaign_plugin.enqueuer.webhook', WebhookEnqueuer::class)
        ->args([
            service('messenger.default_bus'),
            service('webgriffe.sylius_active_campaign_plugin.client_stub.active_campaign.webhook'),
            service('webgriffe.sylius_active_campaign_plugin.generator.channel_hostname_url'),
        ]);
};
