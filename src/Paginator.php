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
    private $lastPage;
    /**
     * @var int
     */
    private $currentPage = 1;
    /**
     * @var array
     */
    private $currentResults;
    /**
     * @var int
     */
    private $numberOfResults;
    /**
     * @var array $queryParameters
     */
    private $queryParameters;
    /**
     * @var array $uriParameters
     */
    private $uriParameters;

    /**
     * @param AdapterInterface $adapter
     * @param array $queryParameters
     * @param array $uriParameters
     */
    public function __construct(AdapterInterface $adapter, array $queryParameters = [], array $uriParameters = [])
    {
        $this->currentPage = isset($queryParameters['page']) ? $queryParameters['page'] : $this->currentPage;
        if (!is_int($this->currentPage)) {
            throw new \InvalidArgumentException('Page number must an integer!');
        }
        $queryParameters['limit'] = isset($queryParameters['limit']) ? $queryParameters['limit'] : 10;
        if (!is_int($queryParameters['limit'])) {
            throw new \InvalidArgumentException('Page limit must an integer!');
        }
        $this->adapter = $adapter;
        $this->queryParameters = $queryParameters;
        $this->queryParameters['page'] = $this->currentPage;
        $this->uriParameters = $uriParameters;
    }

    public function getCurrentPageResults()
    {
        return $this->getCurrentPageResultsAsync()->wait();
    }

    public function getCurrentPageResultsAsync()
    {
        if (!$this->isResultCached()) {
            $this->queryParameters['page'] = $this->currentPage;
            $this->currentResults = $this
                ->adapter
                ->getResultsAsync($this->queryParameters, $this->uriParameters)
                ->then(function ($results) {
                    $this->numberOfResults = $results['total'];
                    $this->lastPage = (int) ceil($this->numberOfResults / $this->queryParameters['limit']);

                    return $results['_embedded']['items'];
                });
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
        if (null === $this->lastPage) {
            if (null === $this->currentResults) {
                throw new \LogicException('You need to fetch a page of results before calling hasNextPage.');
            }

            $this->currentResults->wait();
        }

        return ($this->currentPage < $this->lastPage);
    }

    public function getNumberOfResults()
    {
        if (null === $this->numberOfResults) {
            if (null === $this->currentResults) {
                throw new \LogicException('You need to fetch a page of results before calling getNumberOfResults.');
            }

            $this->currentResults->wait();
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
