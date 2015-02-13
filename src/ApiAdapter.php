<?php

namespace Sylius\Api;

class ApiAdapter implements AdapterInterface
{
    /**
     * @var ApiInterface $api
     */
    private $api;

    function __construct(ApiInterface $api)
    {
        $this->api = $api;
    }

    public function getNumberOfResults()
    {
        $result = $this->api->getPaginated();
        return isset($result['total']) ? $result['total'] : 0;
    }

    public function getResults($page, $limit)
    {
        $result = $this->api->getPaginated($page, $limit);
        return isset($result['_embedded']['items']) ? $result['_embedded']['items'] : array();
    }
}
