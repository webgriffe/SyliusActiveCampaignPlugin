<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\EnqueuableChannelsResolver;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\ContactTagsResolver;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\ChannelCustomerBasedListSubscriptionStatusResolver;
use Webgriffe\SyliusActiveCampaignPlugin\Resolver\CustomerBasedListSubscriptionStatusResolver;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('webgriffe.sylius_active_campaign_plugin.resolver.enqueuable_channels', EnqueuableChannelsResolver::class)
        ->arg('$channelRepository', service('sylius.repository.channel'))
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.resolver.contact_tags', ContactTagsResolver::class);

    $services->set('webgriffe.sylius_active_campaign_plugin.resolver.channel_customer_based_list_subscription_status', ChannelCustomerBasedListSubscriptionStatusResolver::class);

    $services->set('webgriffe.sylius_active_campaign_plugin.resolver.customer_based_list_subscription_status', CustomerBasedListSubscriptionStatusResolver::class);
};
