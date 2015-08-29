<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use ChaosTangent\FansubEbooks\Event\SearchEvent,
    ChaosTangent\FansubEbooks\Event\SearchEvents;

/**
 * Search controller
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class SearchController extends Controller
{
    /**
     * @Route("/search.{_format}", name="search",
     *      requirements={"_format": "|rss|atom"},
     *      defaults={"_format": "html"}
     * )
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Search:index.html.twig")
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
            $lineResults = $lineRepo->search($query, $page, 30);

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

        $viewData = [
            'query' => $query,
            'series_results' => $seriesResults,
            'line_results' => $lineResults,
            'line_results_serialized' => $lineResultsSerialized,
            'search_time' => $searchTime,
        ];

        if ($request->getRequestFormat() == 'rss') {
            return $this->render('ChaosTangentFansubEbooksAppBundle:Search:index.rss.twig', $viewData);
        } else if ($request->getRequestFormat() == 'atom') {
            return $this->render('ChaosTangentFansubEbooksAppBundle:Search:index.atom.twig', $viewData);
        }

        return $viewData;
    }

    /**
     * @Route("/opensearch.xml", name="search_opensearch", defaults={"_format": "xml"})
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Search:opensearch.xml.twig")
     */
    public function opensearchAction()
    {
        return [];
    }
}
