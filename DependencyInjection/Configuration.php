<?php

namespace CULabs\BugCatchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('cu_labs_bug_catch');

        $rootNode
            ->children()
                ->scalarNode('activate')->defaultTrue()->end()
                ->scalarNode('app_key')->isRequired()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
