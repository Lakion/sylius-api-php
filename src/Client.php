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
        return $this->httpClient->request('GET', $url, ['query' => $queryParameters]);
    }

    /**
     * {@inheritdoc}
     */
    public function patch($url, array $body)
    {
        return $this->httpClient->request('PATCH', $url, ['json' => $body]);
    }

    /**
     * {@inheritdoc}
     */
    public function put($url, array $body)
    {
        return $this->httpClient->request('PUT', $url, ['json' => $body]);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($url)
    {
        return $this->httpClient->request('DELETE', $url);
    }

    /**
     * {@inheritdoc}
     */
    public function post($url, $body, array $files = [])
    {
        $options = ['json' => $body];
        foreach ($files as $key => $filePath) {
            $options['multipart'][] = [
                'name' => $key,
                'contents' => $filePath,
            ];
        }

        return $this->httpClient->request('POST', $url, $options);
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
