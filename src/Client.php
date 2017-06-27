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

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Psr7\Uri;

/**
 * Sylius API client
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class Client implements ClientInterface
{
    /**
     * @var Uri $uri
     */
    private $uri;
    /**
     * @var HttpClientInterface $httpClient
     */
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->uri = $httpClient->getConfig('base_uri');
    }

    /**
     * {@inheritdoc}
     */
    public function get($url, array $queryParameters = [])
    {
        return $this->getAsync($url, $queryParameters)->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function getAsync($url, array $queryParameters = [])
    {
        return $this->httpClient->requestAsync('GET', $url, ['query' => $queryParameters]);
    }

    /**
     * {@inheritdoc}
     */
    public function patch($url, array $body)
    {
        return $this->patchAsync($url, $body)->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function patchAsync($url, array $body)
    {
        return $this->httpClient->requestAsync('PATCH', $url, ['json' => $body]);
    }

    /**
     * {@inheritdoc}
     */
    public function put($url, array $body)
    {
        return $this->putAsync($url, $body)->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function putAsync($url, array $body)
    {
        return $this->httpClient->requestAsync('PUT', $url, ['json' => $body]);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($url)
    {
        return $this->deleteAsync($url)->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAsync($url)
    {
        return $this->httpClient->requestAsync('DELETE', $url);
    }

    /**
     * {@inheritdoc}
     */
    public function post($url, $body, array $files = [])
    {
        return $this->postAsync($url, $body, $files)->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function postAsync($url, $body, array $files = [])
    {
        $options = ['json' => $body];
        foreach ($files as $key => $filePath) {
            $options['multipart'][] = [
                'name' => $key,
                'contents' => $filePath,
            ];
        }

        return $this->httpClient->requestAsync('POST', $url, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getSchemeAndHost()
    {
        return sprintf('%s://%s', $this->uri->getScheme(), $this->uri->getHost());
    }

    /**
     * @param  string $url
     * @param  array $options
     * @return Client
     */
    public static function createFromUrl($url, array $options = [])
    {
        $options['base_uri'] = $url;
        self::resolveDefaults($options);

        return new self(new HttpClient($options));
    }

    private static function resolveDefaults(array &$options)
    {
        $options['headers']['User-Agent'] = isset($options['headers']['User-Agent']) ? $options['headers']['User-Agent'] : 'SyliusApi/0.1';
        $options['headers']['Accept'] = isset($options['headers']['Accept']) ? $options['headers']['Accept'] : 'application/json';
        $options['http_errors'] = isset($options['http_errors']) ? $options['http_errors'] : false;
    }
}
