<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\Definition;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Definition for config.yml
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ConfigDefinition implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(0);

        $rootNode->children()
            ->scalarNode('source')
            ->end()
            ->scalarNode('destination')
            ->end()
            ->scalarNode('posts')
            ->end()
            ->scalarNode('includes')
            ->end()
            ->scalarNode('layouts')
            ->end()
            ->scalarNode('plugins')
            ->end()
            ->arrayNode('include')
                ->prototype('scalar')
                ->end()
            ->end()
            ->arrayNode('exclude')
                ->prototype('scalar')
                ->end()
            ->end()
            ->arrayNode('markdown_ext')
                ->prototype('scalar')
                ->end()
            ->end()
            ->arrayNode('processable_ext')
                ->prototype('scalar')
                ->end()
            ->end()
            ->arrayNode('layout_ext')
                ->prototype('scalar')
                ->end()
            ->end()
            ->scalarNode('permalink')
            ->end()
            ->booleanNode('relative_permalinks')
            ->end()
            ->booleanNode('drafts')
            ->end()
            ->variableNode('timezone')
            ->end()
            ->integerNode('paginate')
                ->min(0)
            ->end()
            ->scalarNode('paginate_path')
            ->end()
            ->integerNode('limit_posts')
                ->min(0)
            ->end()
            ->booleanNode('safe')
            ->end()
            ->variableNode('env')
            ->end()
            ->scalarNode('host')
            ->end()
            ->integerNode('port')
                ->min(0)->max(6534)
            ->end()
            ->scalarNode('baseurl')
            ->end()
            ->scalarNode('url')
            ->end()
        ->end();

        return $treeBuilder;
    }
}
