<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Lines controller
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 * @Route("", requirements={"id": "\d+"})
 */
class LinesController extends Controller
{
    /**
     * @Route("/l/{id}.{_format}", name="line",
     *      requirements={"id": "\d+", "_format": "|json"},
     *      defaults={"_format": "html"}
     * )
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Series:search.html.twig")
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * @Route("/l/{id}/voteup.{_format}", name="line_voteup",
     *      requirements={"id": "\d+", "_format": "|json"},
     *      defaults={"_format": "html"}
     * )
     * @Method({"POST"})
     */
    public function voteUpAction()
    {
        return [];
    }

    /**
     * @Route("/l/{id}/votedown.{_format}", name="line_votedown",
     *      requirements={"id": "\d+", "_format": "|json"},
     *      defaults={"_format": "html"}
     * )
     * @Method({"POST"})
     */
    public function voteDownAction()
    {
        return [];
    }

    /**
     * @Route("/l/{id}/flag.{_format}", name="line_flag",
     *      requirements={"id": "\d+", "_format": "|json"},
     *      defaults={"_format": "html"}
     * )
     * @Method({"POST"})
     */
    public function flagAction()
    {
        return [];
    }

    /**
     * @Route("/random.{_format}", name="random",
     *      requirements={"_format": "|json"},
     *      defaults={"_format": "html"}
     * )
     * @Method({"GET"})
     */
    public function randomAction()
    {
        return [];
    }
}
