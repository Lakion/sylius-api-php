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
use Sylius\Api\ClientInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class GenericApiSpec extends ObjectBehavior
{
    function let(ClientInterface $client)
    {
        $this->beConstructedWith($client, 'uri');
    }

    function it_validates_that_uri_is_given($client)
    {
        $this->shouldThrow('InvalidArgumentException')->during('__construct', [$client, null]);
    }

    function it_validates_that_uri_is_a_string($client)
    {
        $this->shouldThrow('InvalidArgumentException')->during('__construct', [$client, 1]);
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
        $client->get('uri/1')->willReturn($response);
        $client->get('uri/1')->shouldBeCalled();

        $this->get(1)->shouldReturn(['id' => 1, 'name' => 'Resource name']);
    }

    function it_gets_paginated_resources($client, ResponseInterface $response)
    {
        $response->getHeader('Content-Type')->willReturn('application/json');
        $response->json()->willReturn(['a', 'b', 'c']);
        $client->get('uri/?page=1&limit=10')->willReturn($response);
        $client->get('uri/?page=1&limit=10')->shouldBeCalled();

        $this->getPaginated()->shouldReturn(['a', 'b', 'c']);
    }


    function it_gets_paginated_resources_by_page($client, ResponseInterface $response)
    {
        $response->getHeader('Content-Type')->willReturn('application/json');
        $response->json()->willReturn(['a', 'b', 'c']);
        $client->get('uri/?page=3&limit=10')->willReturn($response);
        $client->get('uri/?page=3&limit=10')->shouldBeCalled();

        $this->getPaginated(3)->shouldReturn(['a', 'b', 'c']);
    }

    function it_gets_paginated_resources_by_page_with_limit($client, ResponseInterface $response)
    {
        $response->getHeader('Content-Type')->willReturn('application/json');
        $response->json()->willReturn(['a', 'b', 'c']);
        $client->get('uri/?page=2&limit=15')->willReturn($response);
        $client->get('uri/?page=2&limit=15')->shouldBeCalled();

        $this->getPaginated(2, 15)->shouldReturn(['a', 'b', 'c']);
    }

    function it_creates_resource_with_body($client, ResponseInterface $response)
    {
        $response->getHeader('Content-Type')->willReturn('application/json');
        $response->json()->willReturn(['id' => 1, 'field1' => 'field1Value', 'field2' => 'field2Value']);
        $client->post('uri/', ['field1' => 'field1Value', 'field2' => 'field2Value'], [])->willReturn($response);
        $client->post('uri/', ['field1' => 'field1Value', 'field2' => 'field2Value'], [])->shouldBeCalled();
        $this->create(['field1' => 'field1Value', 'field2' => 'field2Value'])->shouldReturn(['id' => 1, 'field1' => 'field1Value', 'field2' => 'field2Value']);
    }

    function it_creates_resource_with_body_and_files($client, ResponseInterface $response)
    {
        $response->getHeader('Content-Type')->willReturn('application/json');
        $response->json()->willReturn(['id' => 1, 'field1' => 'field1Value', 'field2' => 'field2Value', 'images[0][file]' => 'path/to/file1.jpg']);
        $client->post('uri/', ['field1' => 'field1Value', 'field2' => 'field2Value'], ['images[0][file]' => 'path/to/file1.jpg'])->willReturn($response);
        $client->post('uri/', ['field1' => 'field1Value', 'field2' => 'field2Value'], ['images[0][file]' => 'path/to/file1.jpg'])->shouldBeCalled();
        $this->create(['field1' => 'field1Value', 'field2' => 'field2Value'], ['images[0][file]' => 'path/to/file1.jpg'])
            ->shouldReturn(['id' => 1, 'field1' => 'field1Value', 'field2' => 'field2Value', 'images[0][file]' => 'path/to/file1.jpg']);
    }

    function it_updates_resource_with_body($client, ResponseInterface $response)
    {
        $response->getStatusCode()->willReturn(204);
        $client->patch('uri/1', ['field1' => 'field1Value', 'field2' => 'field2Value'])->willReturn($response);
        $client->patch('uri/1', ['field1' => 'field1Value', 'field2' => 'field2Value'])->shouldBeCalled();
        $this->update(1, ['field1' => 'field1Value', 'field2' => 'field2Value'])->shouldReturn(true);
    }

    function it_returns_false_if_resource_update_was_not_successful($client, ResponseInterface $response)
    {
        $response->getStatusCode()->willReturn(400);
        $client->patch('uri/1', ['field1' => 'field1Value', 'field2' => 'field2Value'])->willReturn($response);
        $client->patch('uri/1', ['field1' => 'field1Value', 'field2' => 'field2Value'])->shouldBeCalled();
        $this->update(1, ['field1' => 'field1Value', 'field2' => 'field2Value'])->shouldReturn(false);
    }

    function it_deletes_resource_by_id($client, ResponseInterface $response)
    {
        $response->getStatusCode()->willReturn(204);
        $client->delete('uri/1')->willReturn($response);
        $client->delete('uri/1')->shouldBeCalled();
        $this->delete(1)->shouldReturn(true);
    }

    function it_returns_false_if_resource_deletion_was_not_successful($client, ResponseInterface $response)
    {
        $response->getStatusCode()->willReturn(400);
        $client->delete('uri/1')->willReturn($response);
        $client->delete('uri/1')->shouldBeCalled();
        $this->delete(1)->shouldReturn(false);
    }
}
