<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Default controller
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class DefaultController extends Controller
{
    /**
     * @Route("", name="homepage")
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Default:index.html.twig")
     */
    public function indexAction()
    {
        $om = $this->get('doctrine')->getManager();

        $tweetRepo = $om->getRepository('Entity:Tweet');
        $latestTweet = $tweetRepo->getLatestTweet();
        $tweetCount = $tweetRepo->getTotal();

        $lineRepo = $om->getRepository('Entity:Line');
        $lineCount = $lineRepo->getTotal();

        return [
            'latest_tweet' => $latestTweet,
            'tweet_count' => $tweetCount,
            'line_count' => $lineCount,
        ];
    }

    /**
     * @Route("/help", name="help")
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Default:help.html.twig")
     */
    public function helpAction()
    {
        return [];
    }

    /**
     * @Route("/search", name="search")
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Default:search.html.twig")
     */
    public function searchAction()
    {
    }
}
