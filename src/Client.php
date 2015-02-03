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
use GuzzleHttp\Url;
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
     * @var Url $baseUrl
     */
    private $baseUrl;
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
        $this->baseUrl = Url::fromString($httpClient->getBaseUrl());
    }

    /**
     * @param  string       $resource Plural name of the resource
     * @return ApiInterface
     */
    public function getApi($resource)
    {
        return new GenericApi($this, $resource);
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

    /**
     * {@inheritdoc }
     */
    public function getSchemeAndHost()
    {
        return sprintf('%s://%s', $this->baseUrl->getScheme(), $this->baseUrl->getHost());
    }

    public static function createFromUrl($url, array $options = [])
    {
        $options['base_url'] = $url;
        self::resolveDefaults($options);
        $httpClient = new \GuzzleHttp\Client($options);
        return new self($httpClient);
    }

    private static function resolveDefaults(array &$options)
    {
        $options['defaults']['headers']['User-Agent'] = isset($options['defaults']['headers']['User-Agent']) ? $options['defaults']['headers']['User-Agent'] : 'SyliusApi/0.1';
        $options['defaults']['headers']['Accept'] = isset($options['defaults']['headers']['Accept']) ? $options['defaults']['headers']['Accept'] : 'application/json';
    }
}
