<?php

namespace ChaosTangent\FansubEbooks\Bundle\QuizBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use ChaosTangent\FansubEbooks\Bundle\QuizBundle\DependencyInjection\Security\Factory\TwitterFactory;

class ChaosTangentFansubEbooksQuizBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new TwitterFactory());
    }
}
