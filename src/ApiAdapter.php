<?php

namespace Sylius\Api;

class ApiAdapter implements AdapterInterface
{
    /**
     * @var ApiInterface $api
     */
    private $api;

    public function __construct(ApiInterface $api)
    {
        $this->api = $api;
    }

    public function getNumberOfResults(array $uriParameters = [])
    {
        $result = $this->api->getPaginated(['page' => 1, 'limit' => 1], $uriParameters);

        return isset($result['total']) ? $result['total'] : 0;
    }

    public function getResults(array $queryParameters, array $uriParameters = [])
    {
        $result = $this->api->getPaginated($queryParameters, $uriParameters);

        return isset($result['_embedded']['items']) ? $result['_embedded']['items'] : array();
    }
}
