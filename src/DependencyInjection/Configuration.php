<?php


namespace Hurnell\PostcodeApiBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('hurnell_postcode_api');
        $rootNode
            ->children()
            ->scalarNode('api_key')->isRequired()->info('You\'re postcodeapi.nu api key')->end()
            ->end();

        return $treeBuilder;
    }
}
