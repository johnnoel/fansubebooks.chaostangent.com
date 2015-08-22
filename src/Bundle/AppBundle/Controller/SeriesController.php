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
    ChaosTangent\FansubEbooks\Entity\File,
    ChaosTangent\FansubEbooks\Entity\Line;
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
        $om = $this->get('doctrine')->getManager();

        $fileRepo = $om->getRepository('Entity:File');
        $file = $series->getFiles()->first();

        $lineRepo = $om->getRepository(Line::class);
        $lines = $lineRepo->getLinesByFile($file, 1, 50);

        $serializer = $this->get('jms_serializer');
        $context = $this->get('fansubebooks.serializer.context');

        return [
            'series' => $series,
            'selected_file' => $file,
            'lines' => $lines,
            'lines_serialized' => $serializer->serialize($lines->getResults(), 'json', $context),
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
     * @ParamConverter("file", class="Entity:File", options={"id": "file_id"})
     */
    public function fileAction(Series $series, File $file, Request $request)
    {
        $page = intval($request->query->get('page', 1));

        $lineRepo = $this->get('doctrine')->getManager()->getRepository(Line::class);
        $lines = $lineRepo->getLinesByFile($file, $page, 50);

        $serializer = $this->get('jms_serializer');
        $context = $this->get('fansubebooks.serializer.context');

        if ($request->getRequestFormat() == 'json') {
            $file->setLines($lines->getResults())
                ->setLineCount($lines->getTotal());

            return new Response($serializer->serialize($file, 'json', $context), 200, [
                'Content-Type' => 'application/json',
            ]);
        }

        return [
            'series' => $series,
            'selected_file' => $file,
            'lines' => $lines,
            'lines_serialized' => $serializer->serialize($lines->getResults(), 'json', $context),
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

        $serializer = $this->get('jms_serializer');
        $context = $this->get('fansubebooks.serializer.context');

        if ($request->getRequestFormat() == 'json') {
            return new Response($serializer->serialize($results, 'json', $context), 200, [
                'Content-Type' => 'application/json',
            ]);
        }

        return [
            'query' => $query,
            'series' => $series,
            'results' => $results,
            'search_time' => $searchTime,
            'lines_serialized' => $serializer->serialize($results->getResults(), 'json', $context),
        ];
    }
}
