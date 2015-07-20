<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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
    }

    /**
     * @Route("/{alias}", name="series_view")
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Series:index.html.twig")
     */
    public function seriesAction()
    {
    }

    /**
     * @Route("/{alias}/file/{id}.{_format}", name="series_file",
     *      requirements={"alias": "[^/]+", "_format": "|json"},
     *      defaults={"_format": "html"}
     * )
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Series:file.html.twig")
     */
    public function fileAction()
    {
    }

    /**
     * @Route("/{alias}/search.{_format}", name="series_search",
     *      requirements={"alias": "[^/]+", "_format": "|json"},
     *      defaults={"_format": "html"}
     * )
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Series:search.html.twig")
     */
    public function searchAction()
    {
    }
}
