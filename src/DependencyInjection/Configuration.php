<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\DependencyInjection;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\Form\Type\DefaultResourceType;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomer;
use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomerInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('webgriffe_sylius_active_campaign_plugin');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;

        $this->buildApiClientNode($rootNode);
        $this->buildMapperNode($rootNode);
        $this->buildResourcesNode($rootNode);

        return $treeBuilder;
    }

    private function buildApiClientNode(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('api_client')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('base_url')->isRequired()->cannotBeEmpty()->defaultNull()->end()
                        ->scalarNode('key')->isRequired()->cannotBeEmpty()->defaultNull()->end()
                    ->end()
            ->end()
        ;
    }

    private function buildMapperNode(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('mapper')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('ecommerce_order_product')
                            ->children()
                                ->scalarNode('image_type')
                                    ->defaultValue('main')
                                    ->info('Type of the product image to send to ActiveCampaign. If none is specified or the type does not exists on current product then the first image will be used.')
                                ->end()
                                ->scalarNode('image_filter')
                                    ->defaultValue('sylius_medium')
                                    ->info('Liip filter to apply to the image. If none is specified then the original image will be used.')
                                ->end()
                            ->end()
                    ->end()
            ->end()
        ;
    }

    private function buildResourcesNode(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('channel_customer')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ChannelCustomer::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ChannelCustomerInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(DefaultResourceType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
