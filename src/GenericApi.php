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
use Sylius\Api\Factory\ApiAdapterFactory;
use Sylius\Api\Factory\AdapterFactoryInterface;
use Sylius\Api\Factory\PaginatorFactory;
use Sylius\Api\Factory\PaginatorFactoryInterface;

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
     * @var PaginatorFactoryInterface $paginatorFactory
     */
    private $paginatorFactory;

    /**
     * @var AdapterFactoryInterface $apiAdapterFactory
     */
    private $apiAdapterFactory;

    /**
     * @param  ClientInterface            $client
     * @param  string                     $uri
     * @param  AdapterFactoryInterface $apiAdapterFactory
     * @param  PaginatorFactoryInterface  $paginatorFactory
     * @throws \InvalidArgumentException
     */
    public function __construct(ClientInterface $client, $uri, AdapterFactoryInterface $apiAdapterFactory = null, PaginatorFactoryInterface $paginatorFactory = null)
    {
        $this->setUri($uri);
        $this->client = $client;
        $this->apiAdapterFactory = $apiAdapterFactory ?: new ApiAdapterFactory($this);
        $this->paginatorFactory = $paginatorFactory ?: new PaginatorFactory();
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
        if (($uri[strlen($uri) - 1]) !== '/') {
            $uri = sprintf('%s/', $uri);
        }
        $this->uri = $uri;
    }

    /**
     * @param array $uriParameters
     * @return string
     */
    private function getUri(array $uriParameters = [])
    {
        $uri = $this->uri;
        foreach($uriParameters as $uriParameterKey => $uriParameterValue) {
            $uri = str_ireplace(sprintf('{%s}', $uriParameterKey), $uriParameterValue, $uri);
        }
        return $uri;
    }

    /**
     * {@inheritdoc }
     */
    public function get($id, array $uriParameters = [])
    {
        $response = $this->client->get(sprintf('%s%s', $this->getUri($uriParameters), $id));

        return $this->responseToArray($response);
    }

    /**
     * {@inheritdoc }
     */
    public function getAll()
    {
        $paginator = $this->createPaginator(100);
        $results = $paginator->getCurrentPageResults();
        while($paginator->hasNextPage()) {
            $paginator->nextPage();
            $results = array_merge($results, $paginator->getCurrentPageResults());
        }

        return $results;
    }

    /**
     * {@inheritdoc }
     */
    public function getPaginated($page = 1, $limit = 10, array $uriParameters = [])
    {
        $response = $this->client->get(sprintf('%s?page=%d&limit=%d', $this->getUri($uriParameters), $page, $limit));

        return $this->responseToArray($response);
    }

    /**
     * {@inheritdoc }
     */
    public function createPaginator($limit = 10)
    {
        return $this->paginatorFactory->create($this->apiAdapterFactory->create(), $limit);
    }

    /**
     * {@inheritdoc }
     */
    public function create(array $body, array $uriParameters = [], array $files = [])
    {
        $response = $this->client->post($this->getUri($uriParameters), $body, $files);

        return $this->responseToArray($response);
    }

    /**
     * {@inheritdoc }
     */
    public function update($id, array $body, array $uriParameters = [], array $files = [])
    {
        $uri = sprintf('%s%s', $this->getUri($uriParameters), $id);
        if (empty($files)) {
            $response = $this->client->patch($uri, $body);
        } else {
            $response = $this->client->post($uri, $body, $files);
        }

        return (204 === $response->getStatusCode());
    }

    /**
     * {@inheritdoc }
     */
    public function delete($id, array $uriParameters = [])
    {
        $response = $this->client->delete(sprintf('%s%s', $this->getUri($uriParameters), $id));

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
