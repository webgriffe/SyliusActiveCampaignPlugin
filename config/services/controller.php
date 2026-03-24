<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Webgriffe\SyliusActiveCampaignPlugin\Controller\WebhookController;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('webgriffe.sylius_active_campaign_plugin.controller.webhook', WebhookController::class)
        ->public()
        ->args([
            service('sylius.repository.customer'),
            service('webgriffe_sylius_active_campaign_plugin.command_bus'),
        ])
        ->tag('controller.service_arguments')
        ->call('setContainer', [service('service_container')])
    ;
};
