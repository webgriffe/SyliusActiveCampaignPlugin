<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class WebgriffeSyliusActiveCampaignExtension extends Extension
{
    /**
     * @psalm-suppress UnusedVariable
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $fileLocator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader = new XmlFileLoader($container, $fileLocator);

        $container->setParameter(
            'webgriffe.sylius_active_campaign_plugin.serializer.mapping.xml_file_path',
            $fileLocator->locate('serialization.xml')
        );
        $container->setParameter('webgriffe_sylius_active_campaign.api_client.base_url', (string) $config['api_client']['base_url']);
        $container->setParameter('webgriffe_sylius_active_campaign.api_client.key', (string) $config['api_client']['key']);

        $container->setParameter('webgriffe_sylius_active_campaign.mapper.ecommerce_order_product.image_type', $config['mapper']['ecommerce_order_product']['image_type']);

        $loader->load('services.xml');
    }

    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration();
    }
}
