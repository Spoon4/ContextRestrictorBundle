<?php

namespace Sescandell\ContextRestrictorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle
 *
 * @author Stéphane Escandell
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('context_restrictor');

        $rootNode
            ->children()
                // TODO: use an array for multiple restrictions
                ->scalarNode('target_entity')->end()
                ->scalarNode('field_name')->end()
            ->end();

        return $treeBuilder;
    }
}
