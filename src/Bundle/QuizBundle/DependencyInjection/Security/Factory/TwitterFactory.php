<?php

namespace ChaosTangent\FansubEbooks\Bundle\QuizBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\DefinitionDecorator,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Reference;

/**
 * Twitter security factory
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class TwitterFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        // our version of the user provider
        $providerId = 'fansubebooks.quiz.authentication.provider.twitter.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('fansubebooks.quiz.authentication.provider.twitter'))
            ->replaceArgument(0, new Reference($userProvider))
            ->replaceArgument(2, $id)
            ->replaceArgument(3, $config['consumer_key'])
            ->replaceArgument(4, $config['consumer_secret'])
        ;

        // listener
        $listenerId = 'fansubebooks.quiz.authentication.listener.twitter.'.$id;
        $container
            ->setDefinition($listenerId, new DefinitionDecorator('fansubebooks.quiz.authentication.listener.twitter'))
            ->replaceArgument(3, $id)
            ->replaceArgument(4, $config['login_path'])
            ->replaceArgument(5, $config['confirm_path'])
        ;

        return [ $providerId, $listenerId, $defaultEntryPoint ];
    }

    public function getPosition()
    {
        // form?
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'twitter';
    }

    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('login_path')->end()
                ->scalarNode('confirm_path')->end()
                ->scalarNode('consumer_key')->end()
                ->scalarNode('consumer_secret')->end()
            ->end()
        ;
    }
}
