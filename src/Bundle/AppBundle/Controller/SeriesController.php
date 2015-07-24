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
    public function indexAction()
    {
        $seriesRepo = $this->get('doctrine')->getManager()->getRepository('Entity:Series');
        $series = $seriesRepo->findAll();

        return [
            'series' => $series,
        ];
    }

    /**
     * @Route("/{alias}", name="series_view")
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Series:series.html.twig")
     */
    public function seriesAction(Series $series)
    {
        return [
            'series' => $series,
        ];
    }

    /**
     * @Route("/{alias}/file/{file_id}.{_format}", name="series_file",
     *      requirements={"alias": "[^/]+", "_format": "|json"},
     *      defaults={"_format": "html"}
     * )
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Series:series.html.twig")
     * @ParamConverter("file", class="Entity:File", options={"id": "file_id"})
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
     * @Template("ChaosTangentFansubEbooksAppBundle:Series:search.html.twig")
     */
    public function searchAction(Series $series, Request $request)
    {
        $page = intval($request->query->get('page', 1));
        $query = $request->query->get('q', '');

        $results = [];

        if (!empty(trim($query))) {
            $lineRepo = $this->get('doctrine')->getManager()->getRepository('Entity:Line');
            $results = $lineRepo->search($query, $page, 30, $series);
        }

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
        ];
    }
}
