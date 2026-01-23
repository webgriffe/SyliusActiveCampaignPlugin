<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Webgriffe\SyliusActiveCampaignPlugin\Twig\CustomerExtension;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('webgriffe.sylius_active_campaign_plugin.twig.extension.customer', CustomerExtension::class)
        ->arg('$customerContext', service('sylius.context.customer'))
        ->tag('twig.extension')
    ;
};
