<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('webgriffe_sylius_active_campaign_plugin');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $this->buildApiClientNode($rootNode);
        $this->buildMapperNode($rootNode);

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
                                ->scalarNode('image_type')->defaultNull()->end()
                            ->end()
                    ->end()
            ->end()
        ;
    }
}
