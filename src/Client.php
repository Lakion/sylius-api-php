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
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Event\SubscriberInterface;
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
        $this->httpClient = $httpClient;
        $this->postFileFactory = $postFileFactory ?: new PostFileFactory();
        $this->baseUrl = Url::fromString($httpClient->getBaseUrl());
    }

    /**
     * {@inheritdoc }
     */
    public function get($url, array $queryParameters = [])
    {
        return $this->httpClient->get($url, ['query' => $queryParameters]);
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

    /**
     * @param  string                   $url
     * @param  null|SubscriberInterface $oauth2Subscriber
     * @param  array                    $options
     * @return Client
     */
    public static function createFromUrl($url, SubscriberInterface $oauth2Subscriber = null, array $options = [])
    {
        $options['base_url'] = $url;
        self::resolveDefaults($options, $oauth2Subscriber);

        return new self(new HttpClient($options));
    }

    private static function resolveDefaults(array &$options, SubscriberInterface $oauth2Subscriber = null)
    {
        $options['defaults']['headers']['User-Agent'] = isset($options['defaults']['headers']['User-Agent']) ? $options['defaults']['headers']['User-Agent'] : 'SyliusApi/0.1';
        $options['defaults']['headers']['Accept'] = isset($options['defaults']['headers']['Accept']) ? $options['defaults']['headers']['Accept'] : 'application/json';
        $options['defaults']['exceptions'] = isset($options['defaults']['exceptions']) ? $options['defaults']['exceptions'] : false;

        if ($oauth2Subscriber) {
            $options['defaults']['auth'] = 'oauth2';
            $options['defaults']['subscribers'][] = $oauth2Subscriber;
        }
    }
}
