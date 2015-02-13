<?php

namespace Sylius\Api;

class Paginator implements PaginatorInterface
{
    /**
     * @var AdapterInterface $adapter
     */
    private $adapter;
    /**
     * @var int
     */
    private $currentPage = 1;
    /**
     * @var int
     */
    private $lastPage;
    /**
     * @var int
     */
    private $limitPerPage;
    /**
     * @var array
     */
    private $currentResults = null;
    /**
     * @var int
     */
    private $numberOfResults = -1;

    /**
     * @param AdapterInterface $adapter
     * @param int $limitPerPage
     */
    public function __construct(AdapterInterface $adapter, $limitPerPage = 10)
    {
        if (!is_int($limitPerPage)) {
            throw new \InvalidArgumentException('Page limit must be integer!');
        }
        $this->adapter = $adapter;
        $this->limitPerPage = $limitPerPage;
        $this->lastPage = (int) ceil($this->getNumberOfResults() / $this->limitPerPage);
    }

    public function getCurrentPageResults()
    {
        if (!$this->isResultCached()) {
            $this->currentResults = $this->adapter->getResults($this->currentPage, $this->limitPerPage);
        }
        return $this->currentResults;
    }

    private function isResultCached()
    {
        return (null !== $this->currentResults);
    }

    public function previousPage()
    {
        if (!$this->hasPreviousPage()) {
            throw new \LogicException('There is no previous page.');
        }
        $this->currentPage--;
        $this->currentResults = null;
    }

    public function nextPage()
    {
        if (!$this->hasNextPage()) {
            throw new \LogicException('There is no next page.');
        }
        $this->currentPage++;
        $this->currentResults = null;
    }

    public function hasPreviousPage()
    {
        return (1 < $this->currentPage);
    }

    public function hasNextPage()
    {
        return ($this->currentPage < $this->lastPage);
    }

    public function getNumberOfResults()
    {
        if (-1 === $this->numberOfResults) {
            $this->numberOfResults = $this->adapter->getNumberOfResults();
        }
        return $this->numberOfResults;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }
}
