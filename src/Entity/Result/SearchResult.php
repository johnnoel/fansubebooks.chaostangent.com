<?php

namespace ChaosTangent\FansubEbooks\Entity\Result;

use JMS\Serializer\Annotation as Serializer;

/**
 * Search result
 *
 * The result of a search query
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class SearchResult implements \IteratorAggregate, \Countable
{
    /** @var string */
    protected $query;
    /**
     * @var array
     * @Serializer\MaxDepth(1)
     */
    protected $results;
    /** @var integer */
    protected $total;
    /** @var integer */
    protected $page;
    /** @var integer */
    protected $perPage;

    /**
     * @param string $query The text query that created this result
     * @param array $results The results themselves
     * @param integer $total The total results
     * @param integer $page The page of results in $results
     * @param integer $perPage The number of results in each page
     */
    public function __construct($query, array $results, $total, $page, $perPage)
    {
        $this->query = $query;
        $this->results = $results;
        $this->total = $total;
        $this->page = $page;
        $this->perPage = $perPage;
    }

    /**
     * Get the query that created this result
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get the results themselves
     *
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Get how many total results there are
     *
     * @return integer
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Get the page of results
     *
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Get how many results were requested per page
     *
     * May be different from the actual length of theses results
     *
     * @return integer
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * Get the number of pages for this query
     *
     * Effectively $total / $perPage
     *
     * @return integer
     */
    public function getPages()
    {
        return ceil($this->total / $this->perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->results);
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->results);
    }
}
