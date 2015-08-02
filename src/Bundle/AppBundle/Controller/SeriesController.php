<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;
use ChaosTangent\FansubEbooks\Entity\Series,
    ChaosTangent\FansubEbooks\Entity\File;
use ChaosTangent\FansubEbooks\Event\SearchEvent,
    ChaosTangent\FansubEbooks\Event\SearchEvents;

/**
 * Series controller
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 * @Route("/series")
 */
class SeriesController extends Controller
{
    /**
     * @Route("", name="series")
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Series:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $page = intval($request->query->get('page', 1));

        $seriesRepo = $this->get('doctrine')->getManager()->getRepository('Entity:Series');
        $series = $seriesRepo->getAllSeries($page, 15);

        return [
            'series' => $series,
        ];
    }

    /**
     * @Route("/{alias}", name="series_view")
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Series:series.html.twig")
     * @ParamConverter("series", class="Entity:Series", options={"repository_method": "getSeries", "map_method_signature": true})
     */
    public function seriesAction(Series $series)
    {
        $fileRepo = $this->get('doctrine')->getManager()->getRepository('Entity:File');
        $file = $series->getFiles()->first();
        // hmm
        $file = $fileRepo->getFile($file->getId());

        return [
            'series' => $series,
            'selected_file' => $file,
        ];
    }

    /**
     * @Route("/{alias}/file/{file_id}.{_format}", name="series_file",
     *      requirements={"alias": "[^/]+", "_format": "|json"},
     *      defaults={"_format": "html"}
     * )
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Series:series.html.twig")
     * @ParamConverter("series", class="Entity:Series", options={"repository_method": "getSeries", "map_method_signature": true})
     * @ParamConverter("file", class="Entity:File", options={"id": "file_id", "repository_method": "getFile"})
     */
    public function fileAction(Series $series, File $file, Request $request)
    {
        if ($request->getRequestFormat() == 'json') {
            $serializer = $this->get('jms_serializer');
            $context = SerializationContext::create()->enableMaxDepthChecks();

            return new Response($serializer->serialize($file, 'json', $context), 200, [
                'Content-Type' => 'application/json',
            ]);
        }

        return [
            'series' => $series,
            'selected_file' => $file,
        ];
    }

    /**
     * @Route("/{alias}/search.{_format}", name="series_search",
     *      requirements={"alias": "[^/]+", "_format": "|json"},
     *      defaults={"_format": "html"}
     * )
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Series:series.html.twig")
     * @ParamConverter("series", class="Entity:Series", options={"repository_method": "getSeries", "map_method_signature": true})
     */
    public function searchAction(Series $series, Request $request)
    {
        $page = intval($request->query->get('page', 1));
        $query = $request->query->get('q', '');

        $start = microtime(true);

        $lineRepo = $this->get('doctrine')->getManager()->getRepository('Entity:Line');
        $results = $lineRepo->search($query, $page, 30, $series);

        $searchTime = microtime(true) - $start;

        $searchEvent = new SearchEvent($query, $page, $searchTime, $series);
        $this->get('event_dispatcher')->dispatch(SearchEvents::SEARCH_SERIES, $searchEvent);

        if ($request->getRequestFormat() == 'json') {
            $serializer = $this->get('jms_serializer');
            $context = SerializationContext::create()->enableMaxDepthChecks();

            return new Response($serializer->serialize($results, 'json', $context), 200, [
                'Content-Type' => 'application/json',
            ]);
        }

        return [
            'query' => $query,
            'series' => $series,
            'results' => $results,
            'search_time' => $searchTime,
        ];
    }
}
