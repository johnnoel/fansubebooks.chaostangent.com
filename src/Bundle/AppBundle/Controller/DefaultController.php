<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;
use ChaosTangent\FansubEbooks\Event\SearchEvent,
    ChaosTangent\FansubEbooks\Event\SearchEvents;
use ChaosTangent\FansubEbooks\Entity\Series;

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
        $upcoming = $lineRepo->getQueue(3);

        $seriesRepo = $om->getRepository('Entity:Series');
        $updated = $seriesRepo->getMostRecentlyUpdated(4);

        $serializer = $this->get('jms_serializer');
        $context = $this->get('fansubebooks.serializer.context');

        return [
            'latest_tweet' => $latestTweet,
            'tweet_count' => $tweetCount,
            'line_count' => $lineCount,
            'upcoming' => $upcoming,
            'upcoming_serialized' => $serializer->serialize($upcoming, 'json', $context),
            'updated' => $updated,
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
        $lineResultsSerialized = '';
        $searchTime = 0;

        if (!empty(trim($query))) {
            $start = microtime(true);

            $om = $this->get('doctrine')->getManager();
            $lineRepo = $om->getRepository('Entity:Line');
            $lineResults = $lineRepo->search($query, $page);

            if ($page == 1) {
                $seriesRepo = $om->getRepository('Entity:Series');
                $seriesResults = $seriesRepo->search($query);
            }

            $searchTime = microtime(true) - $start;

            $searchEvent = new SearchEvent($query, $page, $searchTime);
            $this->get('event_dispatcher')->dispatch(SearchEvents::SEARCH, $searchEvent);

            $serializer = $this->get('jms_serializer');
            $context = $this->get('fansubebooks.serializer.context');
            $lineResultsSerialized = $serializer->serialize($lineResults->getResults(), 'json', $context);
        }

        return [
            'query' => $query,
            'series_results' => $seriesResults,
            'line_results' => $lineResults,
            'line_results_serialized' => $lineResultsSerialized,
            'search_time' => $searchTime,
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
