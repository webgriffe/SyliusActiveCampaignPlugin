<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\DependencyInjection;

use Sylius\Bundle\CoreBundle\DependencyInjection\PrependDoctrineMigrationsTrait;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class WebgriffeSyliusActiveCampaignExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    use PrependDoctrineMigrationsTrait;

    /**
     * @psalm-suppress UnusedVariable
     */
    #[\Override]
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $fileLocator = new FileLocator(__DIR__ . '/../../config');
        $loader = new XmlFileLoader($container, $fileLocator);
        $loader2 = new PHPFileLoader($container, $fileLocator);

        $this->registerResources('webgriffe_sylius_active_campaign', $config['driver'], $config['resources'], $container);

        $container->setParameter('webgriffe_sylius_active_campaign.api_client.base_url', (string) $config['api_client']['base_url']);
        $container->setParameter('webgriffe_sylius_active_campaign.api_client.key', (string) $config['api_client']['key']);

        $loader->load('services.xml');
        $loader2->load('services.php');

        $this->addMapperOptionsOnMappers($container, $config);
        $this->addSendUnpaidOrdersOnServices($container, $config);
    }

    #[\Override]
    public function prepend(ContainerBuilder $container): void
    {
        $this->prependDoctrineMigrations($container);
    }

    #[\Override]
    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration();
    }

    #[\Override]
    protected function getMigrationsNamespace(): string
    {
        return 'Webgriffe\SyliusActiveCampaignPlugin\Migrations';
    }

    #[\Override]
    protected function getMigrationsDirectory(): string
    {
        return '@WebgriffeSyliusActiveCampaignPlugin/src/Migrations';
    }

    #[\Override]
    protected function getNamespacesOfMigrationsExecutedBefore(): array
    {
        return ['Sylius\Bundle\CoreBundle\Migrations'];
    }

    private function addMapperOptionsOnMappers(ContainerBuilder $container, array $config): void
    {
        $definition = $container->getDefinition('webgriffe.sylius_active_campaign_plugin.mapper.ecommerce_order_product');
        $definition->setArgument('$imageType', $config['mapper']['ecommerce_order_product']['image_type']);
        $definition->setArgument('$imageFilter', $config['mapper']['ecommerce_order_product']['image_filter']);
    }

    private function addSendUnpaidOrdersOnServices(ContainerBuilder $container, array $config): void
    {
        $definition = $container->getDefinition('webgriffe.sylius_active_campaign_plugin.enqueuer.ecommerce_order');
        $definition->setArgument('$sendUnpaidOrders', $config['send_unpaid_orders']);

        $definition = $container->getDefinition('webgriffe.sylius_active_campaign_plugin.event_subscriber.order');
        $definition->setArgument('$sendUnpaidOrders', $config['send_unpaid_orders']);

        $definition = $container->getDefinition('webgriffe.sylius_active_campaign_plugin.mapper.ecommerce_order');
        $definition->setArgument('$sendUnpaidOrders', $config['send_unpaid_orders']);

        $definition = $container->getDefinition('webgriffe.sylius_active_campaign_plugin.enqueuer.real_time_order');
        $definition->setArgument('$sendUnpaidOrders', $config['send_unpaid_orders']);
    }
}
