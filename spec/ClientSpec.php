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

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7;
use PhpSpec\ObjectBehavior;
use Sylius\Api\Client;
use Sylius\Api\ClientInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */

/**
 * @mixin Client
 */
class ClientSpec extends ObjectBehavior
{
    function let(GuzzleClientInterface $httpClient)
    {
        $httpClient->getConfig('base_uri')->willReturn(Psr7\uri_for('http://demo.sylius.org/api/'));
        $this->beConstructedWith($httpClient);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Api\Client');
    }

    function it_implements_client_interface()
    {
        $this->shouldImplement(ClientInterface::class);
    }

    function it_creates_client_from_url()
    {
        $this::createFromUrl('http://demo.sylius.org/api/')->shouldReturnAnInstanceOf(ClientInterface::class);
    }

    function it_creates_client_from_url_with_scheme_and_host()
    {
        $this::createFromUrl('http://demo.sylius.org/api/')->getSchemeAndHost()->shouldReturn('http://demo.sylius.org');
    }

    function it_sends_get_request_to_the_given_url(GuzzleClientInterface $httpClient, Promise $promise)
    {
        $response = ['foo' => 'bar'];
        $httpClient->requestAsync('GET', '/uri', ['query' => []])->shouldBeCalled()->willReturn($promise);
        $promise->wait()->shouldBeCalled()->willReturn($response);
        $this->get('/uri')->shouldReturn($response);
    }

    function it_sends_async_get_requests_to_the_given_url(GuzzleClientInterface $httpClient, Promise $promise)
    {
        $httpClient->requestAsync('GET', '/uri', ['query' => []])->shouldBeCalled()->willReturn($promise);
        $this->getAsync('/uri')->shouldReturn($promise);
    }

    function it_sends_post_request_to_the_given_url_with_a_given_body(GuzzleClientInterface $httpClient, Promise $promise)
    {
        $response = ['foo' => 'bar'];
        $httpClient->requestAsync('POST', '/uri', ['json' => ['key' => 'value']])->shouldBeCalled()->willReturn($promise);
        $promise->wait()->shouldBeCalled()->willReturn($response);
        $this->post('/uri', ['key' => 'value'])->shouldReturn($response);
    }

    function it_sends_async_post_request_to_the_given_url_with_a_given_body(GuzzleClientInterface $httpClient, Promise $promise)
    {
        $httpClient->requestAsync('POST', '/uri', ['json' => ['key' => 'value']])->shouldBeCalled()->willReturn($promise);
        $this->postAsync('/uri', ['key' => 'value'])->shouldReturn($promise);
    }

    function it_sends_post_request_to_the_given_url_with_a_given_body_with_given_files(
        GuzzleClientInterface $httpClient,
        Promise $promise
    ) {
        $response = ['foo' => 'bar'];
        $httpClient->requestAsync('POST', '/uri', [
            'json' => ['key' => 'value'],
            'multipart' => [
                ['name' => 'images[0][file]', 'contents' => 'path/to/file1.jpg'],
                ['name' => 'images[1][file]', 'contents' => 'path/to/file2.jpg'],
            ],
        ])->shouldBeCalled()->willReturn($promise);

        $promise->wait()->shouldBeCalled()->willReturn($response);

        $this->post(
            '/uri',
            ['key' => 'value'],
            ['images[0][file]' => 'path/to/file1.jpg', 'images[1][file]' => 'path/to/file2.jpg']
        )->shouldReturn($response);
    }

    function it_sends_patch_request_to_the_given_url_with_a_given_body(GuzzleClientInterface $httpClient, Promise $promise)
    {
        $response = ['foo' => 'bar'];
        $httpClient->requestAsync('PATCH', '/uri', ['json' => ['key' => 'value']])->shouldBeCalled()->willReturn($promise);
        $promise->wait()->shouldBeCalled()->willReturn($response);
        $this->patch('/uri', ['key' => 'value'])->shouldReturn($response);
    }

    function it_sends_async_patch_request_to_the_given_url_with_a_given_body(GuzzleClientInterface $httpClient, Promise $promise)
    {
        $httpClient->requestAsync('PATCH', '/uri', ['json' => ['key' => 'value']])->shouldBeCalled()->willReturn($promise);
        $this->patchAsync('/uri', ['key' => 'value'])->shouldReturn($promise);
    }

    function it_sends_put_request_to_the_given_url_with_a_given_body(GuzzleClientInterface $httpClient, Promise $promise)
    {
        $response = ['foo' => 'bar'];
        $httpClient->requestAsync('PUT', '/uri', ['json' => ['key' => 'value']])->shouldBeCalled()->willReturn($promise);
        $promise->wait()->shouldBeCalled()->willReturn($response);
        $this->put('/uri', ['key' => 'value'])->shouldReturn($response);
    }

    function it_sends_async_put_request_to_the_given_url_with_a_given_body(GuzzleClientInterface $httpClient, Promise $promise)
    {
        $httpClient->requestAsync('PUT', '/uri', ['json' => ['key' => 'value']])->shouldBeCalled()->willReturn($promise);
        $this->putAsync('/uri', ['key' => 'value'])->shouldReturn($promise);
    }

    function it_sends_delete_request_to_the_given_url(GuzzleClientInterface $httpClient, Promise $promise)
    {
        $response = ['foo' => 'bar'];
        $httpClient->requestAsync('DELETE', '/uri')->shouldBeCalled()->willReturn($promise);
        $promise->wait()->shouldBeCalled()->willReturn($response);
        $this->delete('/uri')->shouldReturn($response);
    }

    function it_sends_async_delete_request_to_the_given_url(GuzzleClientInterface $httpClient, Promise $promise)
    {
        $httpClient->requestAsync('DELETE', '/uri')->shouldBeCalled()->willReturn($promise);
        $this->deleteAsync('/uri')->shouldReturn($promise);
    }

    function it_gets_scheme_and_host()
    {
        $this->getSchemeAndHost()->shouldReturn('http://demo.sylius.org');
    }
}
