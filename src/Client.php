<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Api;

use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Post\PostBodyInterface;
use GuzzleHttp\Url;
use Sylius\Api\Factory\PostFileFactory;
use Sylius\Api\Factory\PostFileFactoryInterface;

/**
 * Sylius API client
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class Client
{
    /**
     * @var HttpClientInterface $httpClient
     */
    private $httpClient;
    private $postFileFactory;

    public function __construct(HttpClientInterface $httpClient, PostFileFactoryInterface $postFileFactory = null)
    {
        $this->postFileFactory = $postFileFactory ?: new PostFileFactory();
        $this->httpClient = $httpClient;
    }

    /**
     * @param  string|Url        $url URL or URI template
     * @return ResponseInterface
     */
    public function get($url)
    {
        return $this->httpClient->get($url);
    }

    /**
     * @param  string|Url        $url  URL or URI template
     * @param  array             $body
     * @return ResponseInterface
     */
    public function patch($url, array $body)
    {
        return $this->httpClient->patch($url, ['body' => $body]);
    }

    /**
     * @param  string|Url        $url  URL or URI template
     * @param  array             $body
     * @return ResponseInterface
     */
    public function put($url, array $body)
    {
        return $this->httpClient->put($url, ['body' => $body]);
    }

    /**
     * @param  string|Url        $url URL or URI template
     * @return ResponseInterface
     */
    public function delete($url)
    {
        return $this->httpClient->delete($url);
    }

    /**
     * @param  string|Url        $url   URL or URI template
     * @param  array             $body
     * @param  array             $files
     * @return ResponseInterface
     */
    public function post($url, $body, array $files = array())
    {
        $request = $this->httpClient->createRequest('POST', $url, ['body' => $body]);
        /** @var PostBodyInterface $postBody */
        $postBody = $request->getBody();
        foreach ($files as $key => $filePath) {
            $file = $this->postFileFactory->create($key, $filePath);
            $postBody->addFile($file);
        }
        $response = $this->httpClient->send($request);

        return $response;
    }
}
