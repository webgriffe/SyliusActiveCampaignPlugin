<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Webgriffe\SyliusActiveCampaignPlugin\Updater\ChannelCustomerBasedListSubscriptionStatusUpdater;
use Webgriffe\SyliusActiveCampaignPlugin\Updater\CustomerBasedListSubscriptionStatusUpdater;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('webgriffe.sylius_active_campaign_plugin.updater.channel_customer_based_list_subscription_status', ChannelCustomerBasedListSubscriptionStatusUpdater::class)
        ->args([
            service('webgriffe_sylius_active_campaign.repository.channel_customer'),
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.updater.customer_based_list_subscription_status', CustomerBasedListSubscriptionStatusUpdater::class)
        ->args([
            service('sylius.repository.customer'),
        ])
    ;
};
