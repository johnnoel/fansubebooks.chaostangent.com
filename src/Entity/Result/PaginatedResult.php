<?php

namespace ChaosTangent\FansubEbooks\Entity\Result;

use JMS\Serializer\Annotation as Serializer;

/**
 * Paginated result
 *
 * @author John Noel <john.noel@chaostangent>
 * @package FansubEbooks
 */
class PaginatedResult implements \IteratorAggregate, \Countable, \ArrayAccess
{
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
     * @param array $results The results themselves
     * @param integer $total The total results
     * @param integer $page The page of results in $results
     * @param integer $perPage The number of results in each page
     */
    public function __construct(array $results, $total, $page, $perPage)
    {
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

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->results);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return $this->results[$offset];
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        return \RuntimeException('A PaginatedResult is read only after creation');
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        return \RuntimeException('A PaginatedResult is read only after creation');
    }
}
