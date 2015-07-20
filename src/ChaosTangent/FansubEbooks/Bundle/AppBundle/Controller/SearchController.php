<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Search controller
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class SearchController extends Controller
{
    /**
     * @Route("/search", name="search")
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksBundle:Search:index.html")
     */
    public function indexAction()
    {
        return [];
    }
}
