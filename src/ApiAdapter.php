<?php

namespace Sylius\Api;

use GuzzleHttp\Promise\Promise;

class ApiAdapter implements AdapterInterface
{
    /**
     * @var ApiInterface $api
     */
    private $api;

    /**
     * @var array
     */
    private $cachedResults;

    /**
     * @param ApiInterface $api
     */
    public function __construct(ApiInterface $api)
    {
        $this->api = $api;
    }

    /**
     * {@inheritdoc}
     */
    public function getNumberOfResults(array $queryParameters, array $uriParameters = [])
    {
        $result = $this->getResult($queryParameters, $uriParameters, true)->wait();

        return isset($result['total']) ? $result['total'] : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getResults(array $queryParameters, array $uriParameters = [])
    {
        return $this->getResultsAsync($queryParameters, $uriParameters)->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function getResultsAsync(array $queryParameters, array $uriParameters = [])
    {
        return $this
            ->getResult($queryParameters, $uriParameters)
            ->then(function ($result) {
                return isset($result['_embedded']['items'])
                    ? $result['_embedded']['items']
                    : [];
            });
    }

    /**
     * @param array $queryParameters
     * @param array $uriParameters
     * @param bool $cacheResult
     *
     * @return Promise
     */
    private function getResult(array $queryParameters, array $uriParameters, $cacheResult = false)
    {
        $hash = md5(serialize($queryParameters) . serialize($uriParameters));
        if (isset($this->cachedResults[$hash])) {
            return $this->cachedResults[$hash];
        }

        $result = $this->api->getPaginatedAsync($queryParameters, $uriParameters);
        if ($cacheResult) {
            $this->cachedResults = [];
            $this->cachedResults[$hash] = $result;
        }

        return $result;
    }
}
