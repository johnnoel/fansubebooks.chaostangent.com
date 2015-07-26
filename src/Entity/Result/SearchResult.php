<?php

namespace ChaosTangent\FansubEbooks\Entity\Result;

/**
 * Search result
 *
 * The result of a search query
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class SearchResult extends PaginatedResult
{
    /** @var string */
    protected $query;

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
        parent::__construct($results, $total, $page, $perPage);
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
}
