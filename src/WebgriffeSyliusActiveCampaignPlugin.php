<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

final class WebgriffeSyliusActiveCampaignPlugin extends AbstractResourceBundle
{
    use SyliusPluginTrait;

    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    public function getPath(): string
    {
        return __DIR__;
    }

    protected function getModelNamespace(): string
    {
        return 'Webgriffe\SyliusActiveCampaignPlugin\Model';
    }
}
