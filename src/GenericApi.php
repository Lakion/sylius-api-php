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

use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Promise\Promise;
use Psr\Http\Message\ResponseInterface;
use Sylius\Api\Factory\AdapterFactoryInterface;
use Sylius\Api\Factory\ApiAdapterFactory;
use Sylius\Api\Factory\PaginatorFactory;
use Sylius\Api\Factory\PaginatorFactoryInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class GenericApi implements ApiInterface
{
    /**
     * @var array
     */
    static $formats = [
        'json' => ['application/json', 'application/x-json'],
    ];

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
     * @var JsonDecode $jsonDecoder
     */
    private $jsonDecoder;

    /**
     * @param ClientInterface $client
     * @param string $uri
     * @param null|AdapterFactoryInterface $apiAdapterFactory
     * @param null|PaginatorFactoryInterface $paginatorFactory
     * @param null|JsonDecode $jsonDecoder
     */
    public function __construct(
        ClientInterface $client,
        $uri,
        AdapterFactoryInterface $apiAdapterFactory = null,
        PaginatorFactoryInterface $paginatorFactory = null,
        JsonDecode $jsonDecoder = null
    ) {
        $this->setUri($uri);
        $this->client = $client;
        $this->apiAdapterFactory = $apiAdapterFactory ?: new ApiAdapterFactory($this);
        $this->paginatorFactory = $paginatorFactory ?: new PaginatorFactory();
        $this->jsonDecoder = $jsonDecoder ?: new JsonDecode(true);
    }

    /**
     * @param  string $uri
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
     * @param  array $uriParameters
     * @return string
     */
    private function getUri(array $uriParameters = [])
    {
        $uri = $this->uri;
        foreach ($uriParameters as $uriParameterKey => $uriParameterValue) {
            $uri = str_ireplace(sprintf('{%s}', $uriParameterKey), $uriParameterValue, $uri);
        }

        return $uri;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id, array $queryParameters = [], array $uriParameters = [])
    {
        return $this->getAsync($id, $queryParameters, $uriParameters)->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function getAsync($id, array $queryParameters = [], array $uriParameters = [])
    {
        return $this
            ->client
            ->getAsync(
                sprintf('%s%s', $this->getUri($uriParameters), $id),
                $queryParameters
            )->then(
                $this->responseToArray()
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(array $queryParameters = [], array $uriParameters = [])
    {
        return $this->getAllAsync($queryParameters, $uriParameters)->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllAsync(array $queryParameters = [], array $uriParameters = [], int $concurrency = 1)
    {
        $queryParameters['limit'] = isset($queryParameters['limit']) ? $queryParameters['limit'] : 100;
        $paginator = $this->createPaginator($queryParameters, $uriParameters);

        $promise = new Promise(function() use ($paginator, $concurrency, &$promise) {
            $result = [];

            $promises = (function () use ($paginator) {
                yield $paginator->getCurrentPageResultsAsync();

                while ($paginator->hasNextPage()) {
                    $paginator->nextPage();

                    yield $paginator->getCurrentPageResultsAsync();
                }
            })();

            (new EachPromise($promises, [
                'concurrency' => $concurrency,
                'fulfilled' => function ($response) use (&$result) {
                    $result[] = $response;
                },
            ]))->promise()->wait();

            $return = empty($result)
                ? []
                : call_user_func_array('array_merge', $result);

            $promise->resolve($return);
        });

        return $promise;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginated(array $queryParameters = [], array $uriParameters = [])
    {
        return $this->getPaginatedAsync($queryParameters, $uriParameters)->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginatedAsync(array $queryParameters = [], array $uriParameters = [])
    {
        $queryParameters['page'] = isset($queryParameters['page']) ? $queryParameters['page'] : 1;
        $queryParameters['limit'] = isset($queryParameters['limit']) ? $queryParameters['limit'] : 10;

        return $this
            ->client
            ->getAsync(
                $this->getUri($uriParameters),
                $queryParameters
            )->then(
                $this->responseToArray()
            );
    }

    /**
     * {@inheritdoc}
     */
    public function createPaginator(array $queryParameters = [], array $uriParameters = [])
    {
        $queryParameters['limit'] = isset($queryParameters['limit']) ? $queryParameters['limit'] : 10;

        return $this->paginatorFactory->create($this->apiAdapterFactory->create(), $queryParameters, $uriParameters);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $body, array $uriParameters = [], array $files = [])
    {
        return $this->createAsync($body, $uriParameters, $files)->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function createAsync(array $body, array $uriParameters = [], array $files = [])
    {
        return $this
            ->client
            ->postAsync(
                $this->getUri($uriParameters),
                $body,
                $files
            )
            ->then(
                $this->responseToArray()
            );
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $body, array $uriParameters = [], array $files = [])
    {
        return $this->updateAsync($id, $body, $uriParameters, $files)->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function updateAsync($id, array $body, array $uriParameters = [], array $files = [])
    {
        $uri = sprintf('%s%s', $this->getUri($uriParameters), $id);

        if (empty($files)) {
            return $this
                ->client
                ->patchAsync($uri, $body)
                ->then($this->isNoContent());
        }

        return $this
            ->client
            ->postAsync($uri, $body, $files)
            ->then($this->isNoContent());
    }

    /**
     * {@inheritdoc}
     */
    public function put($id, array $body, array $uriParameters = [])
    {
        return $this->putAsync($id, $body, $uriParameters)->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function putAsync($id, array $body, array $uriParameters = [])
    {
        $uri = sprintf('%s%s', $this->getUri($uriParameters), $id);

        return $this
            ->client
            ->putAsync($uri, $body)
            ->then($this->isNoContent());
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id, array $uriParameters = [])
    {
        return $this->deleteAsync($id, $uriParameters)->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAsync($id, array $uriParameters = [])
    {
        return $this
            ->client
            ->deleteAsync(sprintf('%s%s', $this->getUri($uriParameters), $id))
            ->then($this->isNoContent());
    }

    /**
     * @return \Closure
     */
    private function isNoContent()
    {
        return function ($response) {
            return (204 === $response->getStatusCode());
        };
    }

    /**
     * @return \Closure
     */
    private function responseToArray()
    {
        return function (ResponseInterface $response) {
            $responseType = $this->getResponseType($response);

            return $this->{$responseType}((string)$response->getBody());
        };
    }

    /**
     * @param ResponseInterface $response
     * @return string
     */
    private function getResponseType(ResponseInterface $response)
    {
        $responseContentType = $response->getHeaderLine('Content-Type');
        foreach (self::$formats as $format => $contentTypes) {
            foreach ($contentTypes as $contentType) {
                if (strpos($responseContentType, $contentType) !== false) {
                    return $format;
                }
            }
        }

        throw new InvalidResponseFormatException((string)$response->getBody(), $response->getStatusCode());
    }

    /**
     * @param $body
     * @return mixed
     */
    private function json($body)
    {
        return $this->jsonDecoder->decode($body, 'json');
    }
}
