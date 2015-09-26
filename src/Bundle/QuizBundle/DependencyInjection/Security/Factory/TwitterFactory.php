<?php

namespace ChaosTangent\FansubEbooks\Bundle\QuizBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;
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
class TwitterFactory extends AbstractFactory
{
    public function __construct()
    {
        $this->addOption('consumer_key', '');
        $this->addOption('consumer_secret', '');
    }

    public function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        // auth provider
        $providerId = 'fansubebooks.quiz.authentication.provider.twitter.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('fansubebooks.quiz.authentication.provider.twitter'))
            ->replaceArgument(0, new Reference($userProviderId))
            ->replaceArgument(2, $id)
            ->replaceArgument(3, $config['consumer_key'])
            ->replaceArgument(4, $config['consumer_secret'])
        ;

        return $providerId;
    }

    protected function getListenerId()
    {
        return 'fansubebooks.quiz.authentication.listener.twitter';
    }

    protected function createListener($container, $id, $config, $userProvider)
    {
        $listenerId = parent::createListener($container, $id, $config, $userProvider);

        $listener = $container->getDefinition($listenerId);
        $listener->addMethodCall('setAuthProvider', [
            new Reference($this->createAuthProvider($container, $id, $config, $userProvider))
        ]);

        return $listenerId;
    }

    public function getPosition()
    {
        return 'form';
    }

    public function getKey()
    {
        return 'twitter';
    }

    public function addConfiguration(NodeDefinition $node)
    {
        parent::addConfiguration($node);

        $node
            ->children()
                ->scalarNode('consumer_key')->end()
                ->scalarNode('consumer_secret')->end()
            ->end()
        ;
    }
}
