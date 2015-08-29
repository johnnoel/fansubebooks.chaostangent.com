<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;
use ChaosTangent\FansubEbooks\Entity\Series,
    ChaosTangent\FansubEbooks\Entity\Tweet;
use ChaosTangent\FansubEbooks\Bundle\AppBundle\Activity\Entry;

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
        $latestTweets = $tweetRepo->getLatest(1, 4);
        $tweetCount = $tweetRepo->getTotal();

        $lineRepo = $om->getRepository('Entity:Line');
        $lineCount = $lineRepo->getTotal();
        $upcoming = $lineRepo->getQueue(3);

        $seriesRepo = $om->getRepository('Entity:Series');
        $updated = $seriesRepo->getMostRecentlyUpdated(4);

        $activityAgg = $this->get('fansubebooks.activity.aggregator');
        $activity = $activityAgg->getActivity(null, null, 8);

        $serializer = $this->get('jms_serializer');
        $context = $this->get('fansubebooks.serializer.context');

        return [
            'latest_tweets' => $latestTweets,
            'tweet_count' => $tweetCount,
            'line_count' => $lineCount,
            'upcoming' => $upcoming,
            'upcoming_serialized' => $serializer->serialize($upcoming, 'json', $context),
            'updated' => $updated,
            'activity' => $activity,
        ];
    }

    /**
     * @Route("/popular.{_format}", name="popular",
     *      requirements={"_format": "|json"},
     *      defaults={"_format": "html"},
     *      options={"expose": true}
     * )
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Default:popular.html.twig")
     */
    public function popularAction(Request $request)
    {
        $page = intval($request->query->get('page', 1));

        $lineRepo = $this->get('doctrine')->getManager()->getRepository('Entity:Line');
        $lines = $lineRepo->getPopular($page, 50);

        $serializer = $this->get('jms_serializer');
        $context = $this->get('fansubebooks.serializer.context');
        $serialized = $serializer->serialize($lines->getResults(), 'json', $context);

        if ($request->getRequestFormat() == 'json') {
            return new Response($serialized, 200, [
                'Content-Type' => 'application/json',
            ]);
        }

        return [
            'lines' => $lines,
            'lines_serialized' => $serialized,
        ];
    }

    /**
     * @Route("/tweets.{_format}", name="tweets",
     *      requirements={"_format": "|json"},
     *      defaults={"_format": "html"},
     *      options={"expose": true}
     * )
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Default:tweets.html.twig")
     */
    public function tweetsAction(Request $request)
    {
        $page = intval($request->query->get('page', 1));

        $tweetRepo = $this->get('doctrine')->getManager()->getRepository(Tweet::class);
        $tweets = $tweetRepo->getLatest($page, 50);

        return [
            'tweets' => $tweets,
        ];
    }

    /**
     * @Route("/sitemap.xml", name="sitemap", defaults={"_format": "xml"})
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Default:sitemap.xml.twig")
     */
    public function sitemapAction()
    {
        $seriesRepo = $this->get('doctrine')->getManager()->getRepository(Series::class);
        $series = $seriesRepo->findAll();

        return [
            'series' => $series,
        ];
    }
}
