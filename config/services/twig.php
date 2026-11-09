<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Webgriffe\SyliusActiveCampaignPlugin\Twig\CustomerExtension;
use Webgriffe\SyliusActiveCampaignPlugin\Twig\TrackingExtension;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('webgriffe.sylius_active_campaign_plugin.twig.extension.customer', CustomerExtension::class)
        ->arg('$customerContext', service('sylius.context.customer'))
        ->tag('twig.extension')
    ;

    $services->set('webgriffe.sylius_active_campaign_plugin.twig.extension.tracking', TrackingExtension::class)
        ->arg('$trackByDefault', param('webgriffe_sylius_active_campaign.tracking.track_by_default'))
        ->tag('twig.extension')
    ;
};
