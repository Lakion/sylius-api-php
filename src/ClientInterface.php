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

use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\ResponseInterface;

/**
 * Sylius API client interface
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface ClientInterface
{
    /**
     * @param  string|Uri $url URL or URI template
     * @param  array $queryParameters
     * @return ResponseInterface
     */
    public function get($url, array $queryParameters = []);

    /**
     * @param  string|Uri $url URL or URI template
     * @param  array $queryParameters
     * @return Promise
     */
    public function getAsync($url, array $queryParameters = []);

    /**
     * @param  string|Uri $url URL or URI template
     * @param  array $body
     * @return ResponseInterface
     */
    public function patch($url, array $body);

    /**
     * @param  string|Uri $url URL or URI template
     * @param  array $body
     * @return Promise
     */
    public function patchAsync($url, array $body);

    /**
     * @param  string|Uri $url URL or URI template
     * @param  array $body
     * @return ResponseInterface
     */
    public function put($url, array $body);

    /**
     * @param  string|Uri $url URL or URI template
     * @param  array $body
     * @return Promise
     */
    public function putAsync($url, array $body);

    /**
     * @param  string|Uri $url URL or URI template
     * @return ResponseInterface
     */
    public function delete($url);

    /**
     * @param  string|Uri $url URL or URI template
     * @return Promise
     */
    public function deleteAsync($url);

    /**
     * @param  string|Uri $url URL or URI template
     * @param  array $body
     * @param  array $files
     * @return ResponseInterface
     */
    public function post($url, $body, array $files = []);

    /**
     * @param  string|Uri $url URL or URI template
     * @param  array $body
     * @param  array $files
     * @return Promise
     */
    public function postAsync($url, $body, array $files = []);

    /**
     * @return string The scheme and HTTP host
     */
    public function getSchemeAndHost();
}

