<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Webgriffe\SyliusActiveCampaignPlugin\Enqueuer\RealTimeOrderEnqueuer;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe.sylius_active_campaign_plugin.enqueuer.real_time_order', RealTimeOrderEnqueuer::class)
        ->public()
        ->args([
            service('webgriffe.sylius_active_campaign_plugin.enqueuer.ecommerce_order'),
            service('monolog.logger.webgriffe_sylius_active_campaign_plugin'),
        ])
    ;
};
