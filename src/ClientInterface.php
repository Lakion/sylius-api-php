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

use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Url;

/**
 * Sylius API client interface
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface ClientInterface
{
    /**
     * @param  string       $resource Plural name of the resource
     * @return ApiInterface
     */
    public function getApi($resource);

    /**
     * @param  string|Url        $url URL or URI template
     * @return ResponseInterface
     */
    public function get($url);

    /**
     * @param  string|Url        $url  URL or URI template
     * @param  array             $body
     * @return ResponseInterface
     */
    public function patch($url, array $body);

    /**
     * @param  string|Url        $url  URL or URI template
     * @param  array             $body
     * @return ResponseInterface
     */
    public function put($url, array $body);

    /**
     * @param  string|Url        $url URL or URI template
     * @return ResponseInterface
     */
    public function delete($url);

    /**
     * @param  string|Url        $url   URL or URI template
     * @param  array             $body
     * @param  array             $files
     * @return ResponseInterface
     */
    public function post($url, $body, array $files = array());

    /**
     * @return string The scheme and HTTP host
     */
    public function getSchemeAndHost();
}
