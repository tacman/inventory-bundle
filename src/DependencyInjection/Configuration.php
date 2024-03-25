<?php

namespace PlinioCardoso\InventoryBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('inventory');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('out_of_stock_notification')
                    ->children()
                        ->scalarNode('from')->end()
                        ->scalarNode('to')->end()
                        ->scalarNode('subject')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
