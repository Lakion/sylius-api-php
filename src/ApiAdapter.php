<?php

namespace Sylius\Api;

use GuzzleHttp\Promise\PromiseInterface;

class ApiAdapter implements AdapterInterface
{
    /**
     * @var ApiInterface $api
     */
    private $api;

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
    public function getResults(array $queryParameters, array $uriParameters = [])
    {
        return $this->getResultsAsync($queryParameters, $uriParameters)->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function getResultsAsync(array $queryParameters, array $uriParameters = [])
    {
        return $this->api->getPaginatedAsync($queryParameters, $uriParameters);
    }
}
