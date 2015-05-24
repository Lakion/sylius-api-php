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

use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Post\PostBodyInterface;
use GuzzleHttp\Post\PostFileInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Api\Factory\PostFileFactoryInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class ClientSpec extends ObjectBehavior
{
    function let(HttpClientInterface $httpClient, PostFileFactoryInterface $postFileFactory)
    {
        $httpClient->getBaseUrl()->willReturn('http://demo.sylius.org/api/');
        $this->beConstructedWith($httpClient, $postFileFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Api\Client');
    }

    function it_implements_client_interface()
    {
        $this->shouldImplement('Sylius\Api\ClientInterface');
    }

    function it_sends_get_request_to_the_given_url($httpClient)
    {
        $httpClient->get('/uri', ['query' => []])->shouldBeCalled();
        $this->get('/uri');
    }

    function it_sends_post_request_to_the_given_url_with_a_given_body($httpClient, RequestInterface $request)
    {
        $httpClient->createRequest('POST', '/uri', ['body' => ['key' => 'value']])->willReturn($request);
        $httpClient->createRequest('POST', '/uri', ['body' => ['key' => 'value']])->shouldBeCalled();
        $httpClient->send($request)->shouldBeCalled();
        $this->post('/uri', ['key' => 'value']);
    }

    function it_sends_post_request_to_the_given_url_with_a_given_body_with_given_files($httpClient, RequestInterface $request, PostBodyInterface $postbody, $postFileFactory, PostFileInterface $file1, PostFileInterface $file2)
    {
        $httpClient->createRequest('POST', '/uri', ['body' => ['key' => 'value']])->willReturn($request);
        $request->getBody()->willReturn($postbody);
        $postFileFactory->create('images[0][file]', 'path/to/file1.jpg')->willReturn($file1);
        $postFileFactory->create('images[1][file]', 'path/to/file2.jpg')->willReturn($file2);

        $httpClient->createRequest('POST', '/uri', ['body' => ['key' => 'value']])->shouldBeCalled();
        $postbody->addFile($file1)->shouldBeCalled();
        $postbody->addFile($file2)->shouldBeCalled();
        $httpClient->send($request)->shouldBeCalled();

        $this->post('/uri', ['key' => 'value'], ['images[0][file]' => 'path/to/file1.jpg', 'images[1][file]' => 'path/to/file2.jpg']);
    }

    function it_sends_patch_request_to_the_given_url_with_a_given_body($httpClient)
    {
        $httpClient->patch('/uri', ['body' => ['key' => 'value']])->shouldBeCalled();
        $this->patch('/uri', ['key' => 'value']);
    }

    function it_sends_put_request_to_the_given_url_with_a_given_body($httpClient)
    {
        $httpClient->put('/uri', ['body' => ['key' => 'value']])->shouldBeCalled();
        $this->put('/uri', ['key' => 'value']);
    }

    function it_sends_delete_request_to_the_given_url($httpClient)
    {
        $httpClient->delete('/uri')->shouldBeCalled();
        $this->delete('/uri');
    }

    function it_gets_scheme_and_host()
    {
        $this->getSchemeAndHost()->shouldReturn('http://demo.sylius.org');
    }

    function it_creates_client_from_url()
    {
        $this::createFromUrl('http://demo.sylius.org/api/')->shouldReturnAnInstanceOf('Sylius\Api\ClientInterface');
    }

    function it_creates_client_from_url_with_scheme_and_host()
    {
        $this::createFromUrl('http://demo.sylius.org/api/')->getSchemeAndHost()->shouldReturn('http://demo.sylius.org');
    }
}
