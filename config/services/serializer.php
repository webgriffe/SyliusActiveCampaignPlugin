<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Webgriffe\SyliusActiveCampaignPlugin\Serializer\ListResourcesResponseNormalizer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('webgriffe.sylius_active_campaign_plugin.serializer.list_resources_response', ListResourcesResponseNormalizer::class)
        ->arg('$denormalizer', service('serializer.denormalizer.array'))
        ->tag('serializer.normalizer', ['priority' => -400])
    ;
};
