<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface;

/**
 * Post tweet command
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class PostTweetCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('fansubebooks:tweet')
            ->setDescription('Send tweet to Fansub Ebooks account');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $twitter = $this->getContainer()->get('fansubebooks.twitter');
        $lineRepo = $this->getContainer()->get('doctrine')->getManager()
            ->getRepository('Entity:Line');

        $line = $lineRepo->getNextTweetableLine();

        if ($line === null) {
            $output->writeln('<error>Unable to find next tweetable line</error>');
        } else {
            $output->writeln('Tweeting: '.$line->getLine());
            $twitter->tweet($line->getLine());
        }

        return;
    }
}
