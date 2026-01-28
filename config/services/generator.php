<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Webgriffe\SyliusActiveCampaignPlugin\Generator\ChannelHostnameUrlGenerator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('webgriffe.sylius_active_campaign_plugin.generator.channel_hostname_url', ChannelHostnameUrlGenerator::class)
        ->args([
            service('router'),
            service('liip_imagine.cache.manager'),
        ])
    ;
};
