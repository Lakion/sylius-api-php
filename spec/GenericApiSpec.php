<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Api;

use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Sylius\Api\AdapterInterface;
use Sylius\Api\ApiInterface;
use Sylius\Api\ClientInterface;
use Sylius\Api\Factory\AdapterFactoryInterface;
use Sylius\Api\Factory\PaginatorFactoryInterface;
use Sylius\Api\InvalidResponseFormatException;
use Sylius\Api\PaginatorInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class GenericApiSpec extends ObjectBehavior
{
    function let(
        ClientInterface $client,
        AdapterFactoryInterface $adapterFactory,
        PaginatorFactoryInterface $paginatorFactory,
        AdapterInterface $adapter,
        JsonDecode $jsonDecoder
    ) {
        $adapterFactory->create()->willReturn($adapter);
        $this->beConstructedWith($client, 'uri', $adapterFactory, $paginatorFactory, $jsonDecoder);
    }

    function it_validates_that_uri_is_given($client)
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [$client, null]);
    }

    function it_validates_that_uri_is_not_empty($client)
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [$client, '']);
    }

    function it_throws_exception_if_uri_is_not_string($client)
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [$client, 1]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [$client, 1.1]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [$client, true]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [$client, []]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [$client, new \stdClass()]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Api\GenericApi');
    }

    function it_implements_api_interface()
    {
        $this->shouldImplement(ApiInterface::class);
    }

    function it_gets_resource_by_id_async($client, JsonDecode $jsonDecoder, Promise $promise)
    {
        $client->getAsync('uri/1', [])->shouldBeCalled()->willReturn($promise);

        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->getAsync(1)->shouldReturn($promise);
    }

    function it_gets_resource_by_id($client, JsonDecode $jsonDecoder, Promise $promise)
    {
        $return = ['id' => 1, 'name' => 'Resource name'];

        $client->getAsync(
            'uri/1',
            []
        )->shouldBeCalled()->willReturn($promise);

        $promise->wait()->shouldBeCalled()->willReturn($return);
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->get(1)->shouldReturn($return);
    }

    function it_gets_resource_by_id_for_a_specific_uri_with_uri_parameters(
        $client,
        $adapterFactory,
        $paginatorFactory,
        JsonDecode $jsonDecoder,
        Promise $promise
    ) {
        $this->beConstructedWith($client, 'parentUri/{parentId}/uri', $adapterFactory, $paginatorFactory, $jsonDecoder);

        $return = ['id' => 1, 'name' => 'Resource name'];

        $client->getAsync(
            'parentUri/2/uri/1',
            []
        )->willReturn($promise)->shouldBeCalled();

        $promise->wait()->shouldBeCalled()->willReturn($return);
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->get(
            1,
            [],
            ['parentId' => 2]
        )->shouldReturn($return);
    }

    function it_gets_resource_by_id_for_a_specific_uri_with_multiple_uri_parameters(
        $client,
        $adapterFactory,
        $paginatorFactory,
        JsonDecode $jsonDecoder,
        Promise $promise
    ) {
        $this->beConstructedWith(
            $client,
            'parentUri/{parentId}/secondParentUri/{secondParentId}/uri',
            $adapterFactory,
            $paginatorFactory,
            $jsonDecoder
        );

        $return = ['id' => 1, 'name' => 'Resource name'];

        $client->getAsync(
            'parentUri/2/secondParentUri/1/uri/1',
            []
        )->willReturn($promise)->shouldBeCalled();

        $promise->wait()->shouldBeCalled()->willReturn($return);
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->get(
            1,
            [],
            ['parentId' => 2, 'secondParentId' => 1]
        )->shouldReturn($return);
    }

    function it_gets_resource_by_id_with_query_parameters($client, ResponseInterface $response, JsonDecode $jsonDecoder, Promise $promise)
    {
        $return = ['id' => 1, 'name' => 'Resource name'];

        $client->getAsync(
            'uri/1',
            ['foo' => 'bar']
        )->willReturn($promise)->shouldBeCalled();

        $promise->wait()->shouldBeCalled()->willReturn($return);
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->get(
            1,
            ['foo' => 'bar']
        )->shouldReturn($return);
    }

    function it_gets_paginated_resources($client, ResponseInterface $response, JsonDecode $jsonDecoder, Promise $promise)
    {
        $return = ['a', 'b', 'c'];

        $client->getAsync(
            'uri/',
            ['page' => 1, 'limit' => 10]
        )->willReturn($promise)->shouldBeCalled();

        $promise->wait()->shouldBeCalled()->willReturn($return);
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->getPaginated()->shouldReturn(['a', 'b', 'c']);
    }

    function it_gets_paginated_resources_async($client, JsonDecode $jsonDecoder, Promise $promise)
    {
        $client->getAsync(
            'uri/',
            ['page' => 1, 'limit' => 10]
        )->willReturn($promise)->shouldBeCalled();

        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->getPaginatedAsync()->shouldReturn($promise);
    }

    function it_gets_paginated_resources_by_page($client, JsonDecode $jsonDecoder, Promise $promise)
    {
        $return = ['a', 'b', 'c'];

        $client->getAsync(
            'uri/',
            ['page' => 3, 'limit' => 10]
        )->willReturn($promise)->shouldBeCalled();

        $promise->wait()->shouldBeCalled()->willReturn($return);
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->getPaginated(
            ['page' => 3]
        )->shouldReturn($return);
    }

    function it_gets_paginated_resources_by_page_with_limit(
        $client,
        JsonDecode $jsonDecoder,
        Promise $promise
    ) {
        $return = ['a', 'b', 'c'];

        $client->getAsync(
            'uri/',
            ['page' => 2, 'limit' => 15]
        )->willReturn($promise)->shouldBeCalled();

        $promise->wait()->shouldBeCalled()->willReturn($return);
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->getPaginated(
            ['page' => 2, 'limit' => 15]
        )->shouldReturn($return);
    }

    function it_gets_paginated_resources_by_page_with_limit_for_a_specific_uri_with_uri_parameters(
        $client,
        $adapterFactory,
        $paginatorFactory,
        JsonDecode $jsonDecoder,
        Promise $promise
    ) {
        $this->beConstructedWith($client, 'parentUri/{parentId}/uri', $adapterFactory, $paginatorFactory, $jsonDecoder);

        $return = ['a', 'b', 'c'];

        $client->getAsync(
            'parentUri/1/uri/',
            ['page' => 2, 'limit' => 15]
        )->willReturn($promise)->shouldBeCalled();

        $promise->wait()->shouldBeCalled()->willReturn($return);
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->getPaginated(
            ['page' => 2, 'limit' => 15],
            ['parentId' => 1]
        )->shouldReturn($return);
    }

    function it_creates_resource_with_body_async($client, JsonDecode $jsonDecoder, Promise $promise)
    {
        $client->postAsync(
            'uri/',
            ['field1' => 'field1Value', 'field2' => 'field2Value'],
            []
        )->willReturn($promise)->shouldBeCalled();

        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->createAsync(
            ['field1' => 'field1Value', 'field2' => 'field2Value']
        )->shouldReturn($promise);
    }

    function it_creates_resource_with_body($client, JsonDecode $jsonDecoder, Promise $promise)
    {
        $return = ['id' => 1, 'field1' => 'field1Value', 'field2' => 'field2Value'];

        $client->postAsync(
            'uri/',
            ['field1' => 'field1Value', 'field2' => 'field2Value'],
            []
        )->willReturn($promise)->shouldBeCalled();

        $promise->wait()->shouldBeCalled()->willReturn($return);
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->create(
            ['field1' => 'field1Value', 'field2' => 'field2Value']
        )->shouldReturn($return);
    }

    function it_creates_resource_with_body_and_files($client, JsonDecode $jsonDecoder, Promise $promise)
    {
        $return = [
            'id' => 1,
            'field1' => 'field1Value',
            'field2' => 'field2Value',
            'images[0][file]' => 'path/to/file1.jpg',
        ];

        $client->postAsync(
            'uri/',
            ['field1' => 'field1Value', 'field2' => 'field2Value'],
            ['images[0][file]' => 'path/to/file1.jpg']
        )->willReturn($promise)->shouldBeCalled();

        $promise->wait()->shouldBeCalled()->willReturn($return);
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->create(
            ['field1' => 'field1Value', 'field2' => 'field2Value'],
            [],
            ['images[0][file]' => 'path/to/file1.jpg']
        )->shouldReturn($return);
    }

    function it_creates_resource_with_body_for_a_specific_uri_with_uri_parameters(
        $client,
        $adapterFactory,
        $paginatorFactory,
        JsonDecode $jsonDecoder,
        Promise $promise
    ) {
        $return = ['id' => 1, 'field1' => 'field1Value', 'field2' => 'field2Value'];

        $this->beConstructedWith($client, 'parentUri/{parentId}/uri', $adapterFactory, $paginatorFactory, $jsonDecoder);

        $client->postAsync(
            'parentUri/2/uri/',
            ['field1' => 'field1Value', 'field2' => 'field2Value'],
            []
        )->willReturn($promise)->shouldBeCalled();

        $promise->wait()->shouldBeCalled()->willReturn($return);
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->create(
            ['field1' => 'field1Value', 'field2' => 'field2Value'],
            ['parentId' => 2]
        )->shouldReturn($return);
    }

    function it_updates_resource_with_body_async($client, ResponseInterface $response, Promise $promise)
    {
        $client->patchAsync(
            'uri/1',
            ['field1' => 'field1Value', 'field2' => 'field2Value']
        )->willReturn($promise)->shouldBeCalled();

        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->updateAsync(
            1,
            ['field1' => 'field1Value', 'field2' => 'field2Value']
        )->shouldReturn($promise);
    }

    function it_updates_resource_with_body($client, Promise $promise)
    {
        $client->patchAsync(
            'uri/1',
            ['field1' => 'field1Value', 'field2' => 'field2Value']
        )->willReturn($promise)->shouldBeCalled();

        $promise->wait()->shouldBeCalled()->willReturn(true);
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->update(
            1,
            ['field1' => 'field1Value', 'field2' => 'field2Value']
        )->shouldReturn(true);
    }

    function it_puts_resource_with_body_async($client, Promise $promise)
    {
        $client->putAsync(
            'uri/1',
            ['field1' => 'field1Value', 'field2' => 'field2Value']
        )->willReturn($promise)->shouldBeCalled();

        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->putAsync(
            1,
            ['field1' => 'field1Value', 'field2' => 'field2Value']
        )->shouldReturn($promise);
    }

    function it_puts_resource_with_body($client, Promise $promise)
    {
        $client->putAsync(
            'uri/1',
            ['field1' => 'field1Value', 'field2' => 'field2Value']
        )->willReturn($promise)->shouldBeCalled();

        $promise->wait()->shouldBeCalled()->willReturn(true);
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->put(
            1,
            ['field1' => 'field1Value', 'field2' => 'field2Value']
        )->shouldReturn(true);
    }

    function it_updates_resource_with_body_and_files($client, Promise $promise)
    {
        $client->postAsync(
            'uri/1',
            ['field1' => 'field1Value', 'field2' => 'field2Value'],
            ['images[0][file]' => 'path/to/file1.jpg']
        )->willReturn($promise)->shouldBeCalled();

        $promise->wait()->shouldBeCalled()->willReturn(true);
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->update(
            1,
            ['field1' => 'field1Value', 'field2' => 'field2Value'],
            [],
            ['images[0][file]' => 'path/to/file1.jpg']
        )->shouldReturn(true);
    }

    function it_updates_resource_with_body_for_a_specific_uri_with_uri_parameters(
        $client,
        $adapterFactory,
        $paginatorFactory,
        Promise $promise
    ) {
        $this->beConstructedWith($client, 'parentUri/{parentId}/uri', $adapterFactory, $paginatorFactory);

        $client
            ->patchAsync('parentUri/1/uri/2', ['field1' => 'field1Value', 'field2' => 'field2Value'])
            ->willReturn($promise)->shouldBeCalled();

        $promise->wait()->shouldBeCalled()->willReturn(true);
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->update(
            2,
            ['field1' => 'field1Value', 'field2' => 'field2Value'],
            ['parentId' => 1]
        )->shouldReturn(true);
    }

    function it_returns_false_if_resource_update_was_not_successful($client, Promise $promise)
    {
        $client->patchAsync(
            'uri/1',
            ['field1' => 'field1Value', 'field2' => 'field2Value']
        )->willReturn($promise)->shouldBeCalled();

        $promise->wait()->shouldBeCalled()->willReturn(false);
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->update(
            1,
            ['field1' => 'field1Value', 'field2' => 'field2Value']
        )->shouldReturn(false);
    }

    function it_deletes_resource_by_id_async($client, Promise $promise)
    {
        $client->deleteAsync('uri/1')->willReturn($promise)->shouldBeCalled();
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->deleteAsync(1)->shouldReturn($promise);
    }

    function it_deletes_resource_by_id($client, Promise $promise)
    {
        $client->deleteAsync('uri/1')->willReturn($promise)->shouldBeCalled();
        $promise->wait()->shouldBeCalled()->willReturn(true);
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->delete(1)->shouldReturn(true);
    }

    function it_deletes_resource_by_id_for_a_specific_uri_with_uri_parameters(
        $client,
        $adapterFactory,
        $paginatorFactory,
        Promise $promise
    ) {
        $this->beConstructedWith($client, 'parentUri/{parentId}/uri', $adapterFactory, $paginatorFactory);

        $client->deleteAsync('parentUri/1/uri/2')->willReturn($promise)->shouldBeCalled();

        $promise->wait()->shouldBeCalled()->willReturn(true);
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->delete(2, ['parentId' => 1])->shouldReturn(true);
    }

    function it_returns_false_if_resource_deletion_was_not_successful($client, Promise $promise)
    {
        $client->deleteAsync('uri/1')->willReturn($promise)->shouldBeCalled();

        $promise->wait()->shouldBeCalled()->willReturn(false);
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->delete(1)->shouldReturn(false);
    }

    function it_gets_all_resources_async(
        AdapterInterface $adapter,
        PaginatorFactoryInterface $paginatorFactory,
        PaginatorInterface $paginator,
        Promise $promise
    ) {
        $paginatorFactory->create(
            $adapter,
            ['limit' => 100],
            []
        )->willReturn($paginator)->shouldBeCalled();

        $this->getAllAsync()->shouldHaveType(Promise::class);
    }

    function it_gets_all_resources_when_no_results(
        AdapterInterface $adapter,
        PaginatorFactoryInterface $paginatorFactory,
        PaginatorInterface $paginator
    ) {
        $paginatorFactory->create(
            $adapter,
            ['limit' => 100],
            []
        )->willReturn($paginator)->shouldBeCalled();

        $promise = new Promise(function() use (&$promise) {
        });

        $paginator->getCurrentPageResultsAsync()->willReturn($promise);
        $paginator->hasNextPage()->willReturn(false);
        $paginator->nextPage()->shouldNotBeCalled();

        $this->getAll()->shouldReturn([]);
    }

    function it_gets_all_resources(
        AdapterInterface $adapter,
        PaginatorFactoryInterface $paginatorFactory,
        PaginatorInterface $paginator
    ) {
        $paginatorFactory->create(
            $adapter,
            ['limit' => 100],
            []
        )->willReturn($paginator)->shouldBeCalled();

        $promise = new Promise(function() use (&$promise) {
            $promise->resolve(['a', 'b', 'c']);
        });

        $paginator->getCurrentPageResultsAsync()->willReturn($promise);
        $paginator->hasNextPage()->willReturn(false);
        $paginator->nextPage()->shouldNotBeCalled();

        $this->getAll()->shouldReturn(['a', 'b', 'c']);
    }

    function it_gets_all_resources_with_few_pages(
        AdapterInterface $adapter,
        PaginatorFactoryInterface $paginatorFactory,
        PaginatorInterface $paginator
    ) {
        $paginatorFactory->create(
            $adapter,
            ['limit' => 2],
            []
        )->willReturn($paginator)->shouldBeCalled();

        $promiseOne = new Promise(function() use (&$promiseOne) {
            $promiseOne->resolve(['a', 'b']);
        });

        $promiseTwo = new Promise(function() use (&$promiseTwo) {
            $promiseTwo->resolve(['c']);
        });

        $paginator->getCurrentPageResultsAsync()->willReturn($promiseOne, $promiseTwo);
        $paginator->hasNextPage()->willReturn(true, false);
        $paginator->nextPage()->shouldBeCalledTimes(1);

        $this->getAll(['limit' => 2])->shouldReturn(['a', 'b', 'c']);
    }

    function it_gets_all_resources_for_a_specific_uri_with_uri_parameters(
        AdapterInterface $adapter,
        ClientInterface $client,
        AdapterFactoryInterface $adapterFactory,
        PaginatorFactoryInterface $paginatorFactory,
        PaginatorInterface $paginator
    ) {
        $this->beConstructedWith($client, 'parentUri/{parentId}/uri', $adapterFactory, $paginatorFactory);

        $paginatorFactory->create(
            $adapter,
            ['limit' => 100],
            ['parentId' => 1]
        )->willReturn($paginator)->shouldBeCalled();

        $promise = new Promise(function () use (&$promise) {
            $promise->resolve(['a', 'b', 'c']);
        });

        $paginator->getCurrentPageResultsAsync()->willReturn($promise);
        $paginator->hasNextPage()->willReturn(false);

        $this->getAll([], ['parentId' => 1])->shouldReturn(['a', 'b', 'c']);
    }

    function it_creates_paginator_with_defined_limit($adapter, $paginatorFactory, PaginatorInterface $paginator)
    {
        $paginatorFactory->create(
            $adapter,
            ['limit' => 15],
            []
        )->willReturn($paginator)->shouldBeCalled();

        $this->createPaginator(['limit' => 15])->shouldReturn($paginator);
    }

    function it_creates_paginator_with_default_limit($adapter, $paginatorFactory, PaginatorInterface $paginator)
    {
        $paginatorFactory->create(
            $adapter,
            ['limit' => 10],
            []
        )->willReturn($paginator)->shouldBeCalled();

        $this->createPaginator()->shouldReturn($paginator);
    }

    function it_throws_exception_when_invalid_response_format_is_received($client, Promise $promise)
    {
        $exception = new InvalidResponseFormatException('json', 400);

        $client->getAsync('uri/1', [])->shouldBeCalled()->willReturn($promise);

        $promise->wait()->shouldBeCalled()->willThrow($exception);
        $promise->then(Argument::type('callable'))->willReturn($promise);

        $this->shouldThrow($exception)->during('get', [1]);
    }
}
