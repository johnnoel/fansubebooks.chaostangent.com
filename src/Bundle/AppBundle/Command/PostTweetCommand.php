<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;
use ChaosTangent\FansubEbooks\Entity\Tweet;

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

        $om = $this->getContainer()->get('doctrine')->getManager();
        $lineRepo = $om->getRepository('Entity:Line');

        $line = $lineRepo->getNextTweetableLine();

        if ($line === null) {
            $output->writeln('<error>Unable to find next tweetable line</error>');
            return;
        }

        $output->writeln('Tweeting: '.$line->getLine());
        $rawTweet = $twitter->tweet($line->getLine());

        $tweet = new Tweet();
        $tweet->setLine($line)
            ->setTweetId($rawTweet->id_str)
            ->setTweeted(\DateTime::createFromFormat('D M d H:i:s O Y', $rawTweet->created_at));

        $om->persist($tweet);
        $om->flush();
    }
}
