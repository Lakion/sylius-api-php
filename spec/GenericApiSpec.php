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

use GuzzleHttp\Message\ResponseInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Api\AdapterInterface;
use Sylius\Api\ClientInterface;
use Sylius\Api\Factory\AdapterFactoryInterface;
use Sylius\Api\Factory\PaginatorFactoryInterface;
use Sylius\Api\InvalidResponseFormatException;
use Sylius\Api\PaginatorInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class GenericApiSpec extends ObjectBehavior
{
    function let(ClientInterface $client, AdapterFactoryInterface $adapterFactory, PaginatorFactoryInterface $paginatorFactory, AdapterInterface $adapter)
    {
        $adapterFactory->create()->willReturn($adapter);
        $this->beConstructedWith($client, 'uri', $adapterFactory, $paginatorFactory);
    }

    function it_validates_that_uri_is_given($client)
    {
        $this->shouldThrow('InvalidArgumentException')->during('__construct', [$client, null]);
    }

    function it_validates_that_uri_is_not_empty($client)
    {
        $this->shouldThrow('InvalidArgumentException')->during('__construct', [$client, '']);
    }

    function it_throws_exception_if_uri_is_not_string($client)
    {
        $this->shouldThrow('InvalidArgumentException')->during('__construct', [$client, 1]);
        $this->shouldThrow('InvalidArgumentException')->during('__construct', [$client, 1.1]);
        $this->shouldThrow('InvalidArgumentException')->during('__construct', [$client, true]);
        $this->shouldThrow('InvalidArgumentException')->during('__construct', [$client, array()]);
        $this->shouldThrow('InvalidArgumentException')->during('__construct', [$client, new \stdClass()]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Api\GenericApi');
    }

    function it_implements_api_interface()
    {
        $this->shouldImplement('Sylius\Api\ApiInterface');
    }

    function it_gets_resource_by_id($client, ResponseInterface $response)
    {
        $response->getHeader('Content-Type')->willReturn('application/json');
        $response->json()->willReturn(['id' => 1, 'name' => 'Resource name']);
        $client->get('uri/1')->willReturn($response)->shouldBeCalled();

        $this->get(1)->shouldReturn(['id' => 1, 'name' => 'Resource name']);
    }

    function it_gets_resource_by_id_for_a_specific_uri_with_uri_parameters($client, $adapterFactory, $paginatorFactory, ResponseInterface $response)
    {
        $this->beConstructedWith($client, 'parentUri/{parentId}/uri', $adapterFactory, $paginatorFactory);
        $response->getHeader('Content-Type')->willReturn('application/json');
        $response->json()->willReturn(['id' => 1, 'name' => 'Resource name']);
        $client->get('parentUri/2/uri/1')->willReturn($response)->shouldBeCalled();

        $this->get(1, ['parentId' => 2] )->shouldReturn(['id' => 1, 'name' => 'Resource name']);
    }

    function it_gets_resource_by_id_for_a_specific_uri_with_multiple_uri_parameters($client, $adapterFactory, $paginatorFactory, ResponseInterface $response)
    {
        $this->beConstructedWith($client, 'parentUri/{parentId}/secondParentUri/{secondParentId}/uri', $adapterFactory, $paginatorFactory);
        $response->getHeader('Content-Type')->willReturn('application/json');
        $response->json()->willReturn(['id' => 1, 'name' => 'Resource name']);
        $client->get('parentUri/2/secondParentUri/1/uri/1')->willReturn($response)->shouldBeCalled();

        $this->get(1, ['parentId' => 2, 'secondParentId' => 1] )->shouldReturn(['id' => 1, 'name' => 'Resource name']);
    }

    function it_gets_paginated_resources($client, ResponseInterface $response)
    {
        $response->getHeader('Content-Type')->willReturn('application/json');
        $response->json()->willReturn(['a', 'b', 'c']);
        $client->get('uri/', ['page' => 1, 'limit' => 10])->willReturn($response)->shouldBeCalled();

        $this->getPaginated()->shouldReturn(['a', 'b', 'c']);
    }

    function it_gets_paginated_resources_by_page($client, ResponseInterface $response)
    {
        $response->getHeader('Content-Type')->willReturn('application/json');
        $response->json()->willReturn(['a', 'b', 'c']);
        $client->get('uri/', ['page' => 3, 'limit' => 10])->willReturn($response)->shouldBeCalled();

        $this->getPaginated(['page' => 3])->shouldReturn(['a', 'b', 'c']);
    }

    function it_gets_paginated_resources_by_page_with_limit($client, ResponseInterface $response)
    {
        $response->getHeader('Content-Type')->willReturn('application/json');
        $response->json()->willReturn(['a', 'b', 'c']);
        $client->get('uri/', ['page' => 2, 'limit' => 15])->willReturn($response)->shouldBeCalled();

        $this->getPaginated(['page' => 2, 'limit' => 15])->shouldReturn(['a', 'b', 'c']);
    }

    function it_gets_paginated_resources_by_page_with_limit_for_a_specific_uri_with_uri_parameters($client, $adapterFactory, $paginatorFactory, ResponseInterface $response)
    {
        $this->beConstructedWith($client, 'parentUri/{parentId}/uri', $adapterFactory, $paginatorFactory);
        $response->getHeader('Content-Type')->willReturn('application/json');
        $response->json()->willReturn(['a', 'b', 'c']);
        $client->get('parentUri/1/uri/', ['page' => 2, 'limit' => 15])->willReturn($response)->shouldBeCalled();

        $this->getPaginated(['page' => 2, 'limit' => 15], ['parentId' => 1])->shouldReturn(['a', 'b', 'c']);
    }

    function it_creates_resource_with_body($client, ResponseInterface $response)
    {
        $response->getHeader('Content-Type')->willReturn('application/json');
        $response->json()->willReturn(['id' => 1, 'field1' => 'field1Value', 'field2' => 'field2Value']);
        $client->post('uri/', ['field1' => 'field1Value', 'field2' => 'field2Value'], [])
            ->willReturn($response)->shouldBeCalled();

        $this->create(['field1' => 'field1Value', 'field2' => 'field2Value'])->shouldReturn(['id' => 1, 'field1' => 'field1Value', 'field2' => 'field2Value']);
    }

    function it_creates_resource_with_body_and_files($client, ResponseInterface $response)
    {
        $response->getHeader('Content-Type')->willReturn('application/json');
        $response->json()->willReturn(['id' => 1, 'field1' => 'field1Value', 'field2' => 'field2Value', 'images[0][file]' => 'path/to/file1.jpg']);
        $client->post('uri/', ['field1' => 'field1Value', 'field2' => 'field2Value'], ['images[0][file]' => 'path/to/file1.jpg'])
            ->willReturn($response)->shouldBeCalled();

        $this->create(['field1' => 'field1Value', 'field2' => 'field2Value'], [], ['images[0][file]' => 'path/to/file1.jpg'])
            ->shouldReturn(['id' => 1, 'field1' => 'field1Value', 'field2' => 'field2Value', 'images[0][file]' => 'path/to/file1.jpg']);
    }

    function it_creates_resource_with_body_for_a_specific_uri_with_uri_parameters($client, $adapterFactory, $paginatorFactory, ResponseInterface $response)
    {
        $this->beConstructedWith($client, 'parentUri/{parentId}/uri', $adapterFactory, $paginatorFactory);
        $response->getHeader('Content-Type')->willReturn('application/json');
        $response->json()->willReturn(['id' => 1, 'field1' => 'field1Value', 'field2' => 'field2Value']);
        $client->post('parentUri/2/uri/', ['field1' => 'field1Value', 'field2' => 'field2Value'], [])
            ->willReturn($response)->shouldBeCalled();

        $this->create(['field1' => 'field1Value', 'field2' => 'field2Value'], ['parentId' => 2])
            ->shouldReturn(['id' => 1, 'field1' => 'field1Value', 'field2' => 'field2Value']);
    }

    function it_updates_resource_with_body($client, ResponseInterface $response)
    {
        $response->getStatusCode()->willReturn(204);
        $client->patch('uri/1', ['field1' => 'field1Value', 'field2' => 'field2Value'])
            ->willReturn($response)->shouldBeCalled();

        $this->update(1, ['field1' => 'field1Value', 'field2' => 'field2Value'])->shouldReturn(true);
    }

    function it_updates_resource_with_body_and_files($client, ResponseInterface $response)
    {
        $response->getStatusCode()->willReturn(204);
        $client->post('uri/1', ['field1' => 'field1Value', 'field2' => 'field2Value'], ['images[0][file]' => 'path/to/file1.jpg'])
            ->willReturn($response)->shouldBeCalled();

        $this->update(1, ['field1' => 'field1Value', 'field2' => 'field2Value'], [], ['images[0][file]' => 'path/to/file1.jpg'])->shouldReturn(true);
    }

    function it_updates_resource_with_body_for_a_specific_uri_with_uri_parameters($client, $adapterFactory, $paginatorFactory, ResponseInterface $response)
    {
        $this->beConstructedWith($client, 'parentUri/{parentId}/uri', $adapterFactory, $paginatorFactory);
        $response->getStatusCode()->willReturn(204);
        $client->patch('parentUri/1/uri/2', ['field1' => 'field1Value', 'field2' => 'field2Value'])
            ->willReturn($response)->shouldBeCalled();

        $this->update(2, ['field1' => 'field1Value', 'field2' => 'field2Value'], ['parentId' => 1])->shouldReturn(true);
    }

    function it_returns_false_if_resource_update_was_not_successful($client, ResponseInterface $response)
    {
        $response->getStatusCode()->willReturn(400);
        $client->patch('uri/1', ['field1' => 'field1Value', 'field2' => 'field2Value'])->willReturn($response)->shouldBeCalled();

        $this->update(1, ['field1' => 'field1Value', 'field2' => 'field2Value'])->shouldReturn(false);
    }

    function it_deletes_resource_by_id($client, ResponseInterface $response)
    {
        $response->getStatusCode()->willReturn(204);
        $client->delete('uri/1')->willReturn($response)->shouldBeCalled();

        $this->delete(1)->shouldReturn(true);
    }

    function it_deletes_resource_by_id_for_a_specific_uri_with_uri_parameters($client, $adapterFactory, $paginatorFactory, ResponseInterface $response)
    {
        $this->beConstructedWith($client, 'parentUri/{parentId}/uri', $adapterFactory, $paginatorFactory);
        $response->getStatusCode()->willReturn(204);
        $client->delete('parentUri/1/uri/2')->willReturn($response)->shouldBeCalled();

        $this->delete(2, ['parentId' => 1])->shouldReturn(true);
    }

    function it_returns_false_if_resource_deletion_was_not_successful($client, ResponseInterface $response)
    {
        $response->getStatusCode()->willReturn(400);
        $client->delete('uri/1')->willReturn($response)->shouldBeCalled();

        $this->delete(1)->shouldReturn(false);
    }

    function it_gets_all_resources($adapter, $paginatorFactory, PaginatorInterface $paginator)
    {
        $paginatorFactory->create($adapter, ['limit' => 100], [])->willReturn($paginator)->shouldBeCalled();
        $paginator->getCurrentPageResults()->willReturn(['a', 'b', 'c']);
        $paginator->hasNextPage()->willReturn(false);
        $paginator->nextPage()->shouldNotBeCalled();

        $this->getAll()->shouldReturn(['a', 'b', 'c']);
    }

    function it_gets_all_resources_with_few_pages($adapter, $paginatorFactory, PaginatorInterface $paginator)
    {
        $paginatorFactory->create($adapter, ['limit' => 2], [])->willReturn($paginator)->shouldBeCalled();
        $paginator->getCurrentPageResults()->willReturn(['a', 'b'], ['c']);
        $paginator->hasNextPage()->willReturn(true, false);
        $paginator->nextPage()->shouldBeCalledTimes(1);

        $this->getAll(['limit' => 2])->shouldReturn(['a', 'b', 'c']);
    }

    function it_gets_all_resources_for_a_specific_uri_with_uri_parameters($adapter, $client, $adapterFactory, $paginatorFactory, PaginatorInterface $paginator)
    {
        $this->beConstructedWith($client, 'parentUri/{parentId}/uri', $adapterFactory, $paginatorFactory);
        $paginatorFactory->create($adapter, ['limit' => 100], ['parentId' => 1])->willReturn($paginator)->shouldBeCalled();
        $paginator->getCurrentPageResults()->willReturn(['a', 'b', 'c']);
        $paginator->hasNextPage()->willReturn(false);

        $this->getAll([], ['parentId' => 1])->shouldReturn(['a', 'b', 'c']);
    }

    function it_creates_paginator_with_defined_limit($adapter, $paginatorFactory, PaginatorInterface $paginator)
    {
        $paginatorFactory->create($adapter, ['limit' => 15], [])->willReturn($paginator)->shouldBeCalled();
        $this->createPaginator(['limit' => 15])->shouldReturn($paginator);
    }

    function it_creates_paginator_with_default_limit($adapter, $paginatorFactory, PaginatorInterface $paginator)
    {
        $paginatorFactory->create($adapter, ['limit' => 10], [])->willReturn($paginator)->shouldBeCalled();
        $this->createPaginator()->shouldReturn($paginator);
    }

    function it_throws_exception_when_invalid_response_format_is_received($client, ResponseInterface $response)
    {
        $response->getHeader('Content-Type')->willReturn('application/xhtml+xml');
        $response->getBody()->willReturn('body');
        $response->getStatusCode()->willReturn(400);
        $client->get('uri/1')->willReturn($response)->shouldBeCalled();

        $this->shouldThrow(new InvalidResponseFormatException('body', 400))->during('get', [1]);
    }
}
