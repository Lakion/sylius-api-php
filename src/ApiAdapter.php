<?php

namespace Sylius\Api;

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

    public function __construct(ApiInterface $api)
    {
        $this->api = $api;
    }

    public function getNumberOfResults(array $queryParameters, array $uriParameters = [])
    {
        $result = $this->getResult($queryParameters, $uriParameters, true);

        return isset($result['total']) ? $result['total'] : 0;
    }

    public function getResults(array $queryParameters, array $uriParameters = [])
    {
        $result = $this->getResult($queryParameters, $uriParameters);

        return isset($result['_embedded']['items']) ? $result['_embedded']['items'] : array();
    }

    /**
     * @param array $queryParameters
     * @param array $uriParameters
     * @param bool  $cacheResult
     *
     * @return array
     */
    private function getResult(array $queryParameters, array $uriParameters, $cacheResult = false)
    {
        $hash = md5(serialize($queryParameters) . serialize($uriParameters));
        if (isset($this->cachedResults[$hash])) {
            return $this->cachedResults[$hash];
        }

        $result = $this->api->getPaginated($queryParameters, $uriParameters);
        if ($cacheResult) {
            $this->cachedResults = array();
            $this->cachedResults[$hash] = $result;
        }

        return $result;
    }
}
