<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Default controller
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class DefaultController extends Controller
{
    /**
     * @Route("", name="home")
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
     * @Route("/search", name="search")
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Default:search.html.twig")
     */
    public function searchAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $query = $request->query->get('q', null);

        $seriesResults = [];
        $lineResults = [];

        if (!empty($query)) {
            $om = $this->get('doctrine')->getManager();
            $lineRepo = $om->getRepository('Entity:Line');
            $lineResults = $lineRepo->search($query, $page);

            if ($page == 1) {
                $seriesRepo = $om->getRepository('Entity:Series');
                $seriesResults = $seriesRepo->search($query);
            }
        }

        return [
            'query' => $query,
            'series_results' => $seriesResults,
            'line_results' => $lineResults,
        ];
    }
}
