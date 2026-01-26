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
use Webmozart\Assert\Assert;

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

        $driver = $config['driver'] ?? null;
        Assert::string($driver, 'Driver must be configured');
        $resources = $config['resources'] ?? null;
        Assert::isArray($resources, 'Resources must be configured as array');
        $this->registerResources('webgriffe_sylius_active_campaign', $driver, $resources, $container);

        /** @psalm-suppress MixedArrayAccess */
        $baseUrl = $config['api_client']['base_url'] ?? null;
        Assert::string($baseUrl, 'You must configure the Active Campaign API base URL. Take a look at the documentation for more details.');
        $container->setParameter('webgriffe_sylius_active_campaign.api_client.base_url', $baseUrl);
        /** @psalm-suppress MixedArrayAccess */
        $key = $config['api_client']['key'] ?? null;
        Assert::string($key, 'You must configure the Active Campaign API key. Take a look at the documentation for more details.');
        $container->setParameter('webgriffe_sylius_active_campaign.api_client.key', $key);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.php');

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
        /** @psalm-suppress MixedArrayAccess */
        $imageType = $config['mapper']['ecommerce_order_product']['image_type'] ?? null;
        Assert::string($imageType);
        $definition->setArgument('$imageType', $imageType);
        /** @psalm-suppress MixedArrayAccess */
        $imageFilter = $config['mapper']['ecommerce_order_product']['image_filter'] ?? null;
        Assert::string($imageFilter);
        $definition->setArgument('$imageFilter', $imageFilter);
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
