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
use GuzzleHttp\Post\PostBodyInterface;
use Sylius\Api\Factory\PostFileFactory;
use Sylius\Api\Factory\PostFileFactoryInterface;

/**
 * Sylius API client
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class Client implements ClientInterface
{
    /**
     * @var HttpClientInterface $httpClient
     */
    private $httpClient;
    /**
     * @var PostFileFactoryInterface $postFileFactory
     */
    private $postFileFactory;

    public function __construct(HttpClientInterface $httpClient, PostFileFactoryInterface $postFileFactory = null)
    {
        $this->postFileFactory = $postFileFactory ?: new PostFileFactory();
        $this->httpClient = $httpClient;
    }

    /**
     * {@inheritdoc }
     */
    public function get($url)
    {
        return $this->httpClient->get($url);
    }

    /**
     * {@inheritdoc }
     */
    public function patch($url, array $body)
    {
        return $this->httpClient->patch($url, ['body' => $body]);
    }

    /**
     * {@inheritdoc }
     */
    public function put($url, array $body)
    {
        return $this->httpClient->put($url, ['body' => $body]);
    }

    /**
     * {@inheritdoc }
     */
    public function delete($url)
    {
        return $this->httpClient->delete($url);
    }

    /**
     * {@inheritdoc }
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
