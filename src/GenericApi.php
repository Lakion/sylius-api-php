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

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class GenericApi implements ApiInterface
{
    /**
     * @var ClientInterface $client
     */
    private $client;
    /**
     * @var string $uri
     */
    private $uri;

    /**
     * @param  ClientInterface           $client
     * @param  string                    $uri
     * @throws \InvalidArgumentException
     */
    public function __construct(ClientInterface $client, $uri)
    {
        $this->setUri($uri);
        $this->client = $client;
    }

    /**
     * @param  string                    $uri
     * @throws \InvalidArgumentException
     */
    private function setUri($uri)
    {
        if (empty($uri) || !is_string($uri)) {
            throw new \InvalidArgumentException('You must specify uri for Api');
        }
        if (($uri[strlen($uri) - 1]) != '/') {
            $uri = sprintf('%s/', $uri);
        }
        $this->uri = strtolower($uri);
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * {@inheritdoc }
     */
    public function get($id)
    {
        $response = $this->client->get(sprintf('%s%s', $this->getUri(), $id));

        return $this->responseToArray($response);
    }

    /**
     * {@inheritdoc }
     */
    public function getPaginated($page = 1, $limit = 10)
    {
        $response = $this->client->get(sprintf('%s?page=%d&limit=%d', $this->getUri(), $page, $limit));

        return $this->responseToArray($response);
    }

    /**
     * {@inheritdoc }
     */
    public function create(array $body, array $files = [])
    {
        $response = $this->client->post($this->getUri(), $body, $files);

        return $this->responseToArray($response);
    }

    /**
     * {@inheritdoc }
     */
    public function update($id, array $body, array $files = [])
    {
        $response = $this->client->patch(sprintf('%s%s', $this->getUri(), $id), $body);

        return (204 === $response->getStatusCode());
    }

    /**
     * {@inheritdoc }
     */
    public function delete($id)
    {
        $response = $this->client->delete(sprintf('%s%s', $this->getUri(), $id));

        return (204 === $response->getStatusCode());
    }

    private function responseToArray(ResponseInterface $response)
    {
        $responseType = $response->getHeader('Content-Type');
        if ((false === strpos($responseType, 'application/json')) && (false === strpos($responseType, 'application/xml'))) {
            throw new InvalidResponseFormatException((string) $response->getBody(), $response->getStatusCode());
        }

        return (strpos($responseType, 'application/json') !== false) ? $response->json() : $response->xml();
    }
}