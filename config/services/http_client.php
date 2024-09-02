<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set('webgriffe.sylius_active_campaign_plugin.http_client.handler', HandlerStack::class)
        ->factory([HandlerStack::class, 'create'])
        ->call('push', [
            service('webgriffe.sylius_active_campaign_plugin.middleware.header'),
            'add_active_campaign_headers',
        ])
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.http_client.active_campaign', Client::class)
        ->args([
            [
                'handler' => service('webgriffe.sylius_active_campaign_plugin.http_client.handler'),
                'base_uri' => param('webgriffe_sylius_active_campaign.api_client.base_url'),
                'http_errors' => false,
            ],
        ])
    ;
};
