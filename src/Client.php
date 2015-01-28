<?php

namespace Sylius\Api;

use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Post\PostFile;
use GuzzleHttp\Url;

class Client
{
    /**
     * @var HttpClientInterface $httpClient
     */
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string|Url $url   URL or URI template
     * @return ResponseInterface
     */
    public function get($url)
    {
        return $this->httpClient->get($url);
    }

    /**
     * @param string|Url $url   URL or URI template
     * @param array $body
     * @return ResponseInterface
     */
    public function patch($url, array $body)
    {
        return $this->httpClient->patch($url, ['body' => $body]);
    }

    /**
     * @param string|Url $url   URL or URI template
     * @param array $body
     * @return ResponseInterface
     */
    public function put($url, array $body)
    {
        return $this->httpClient->put($url, ['body' => $body]);
    }

    /**
     * @param string|Url $url   URL or URI template
     * @return ResponseInterface
     */
    public function delete($url)
    {
        return $this->httpClient->delete($url);
    }
}
