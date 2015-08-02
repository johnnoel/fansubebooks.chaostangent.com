<?php

namespace ChaosTangent\FansubEbooks\Event;

use Symfony\Component\EventDispatcher\Event;
use ChaosTangent\FansubEbooks\Entity\Series;

/**
 * Search event
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class SearchEvent extends Event
{
    /** @var string */
    protected $query;
    /** @var integer */
    protected $page;
    /** @var float */
    protected $time;
    /** @var Series */
    protected $series;

    public function __construct($query, $page = 1, $time = 0.0, Series $series = null)
    {
        $this->query = $query;
        $this->page = $page;
        $this->time = $time;
        $this->series = $series;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getSeries()
    {
        return $this->series;
    }
}
