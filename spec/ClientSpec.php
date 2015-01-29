<?php

namespace spec\Sylius\Api;

use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Post\PostBodyInterface;
use GuzzleHttp\Post\PostFileInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Api\Factory\PostFileFactoryInterface;

class ClientSpec extends ObjectBehavior
{
    function let(HttpClientInterface $httpClient, PostFileFactoryInterface $postFileFactory)
    {
        $this->beConstructedWith($httpClient, $postFileFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Api\Client');
    }

    function it_sends_get_request_to_the_given_url($httpClient)
    {
        $httpClient->get('/uri')->shouldBeCalled();
        $this->get('/uri');
    }

    function it_sends_post_request_to_the_given_url_with_a_given_body($httpClient, Request $request)
    {
        $httpClient->createRequest('POST', '/uri', ['body' => ['key' => 'value']])->willReturn($request);
        $httpClient->createRequest('POST', '/uri', ['body' => ['key' => 'value']])->shouldBeCalled();
        $httpClient->send($request)->shouldBeCalled();
        $this->post('/uri', ['key' => 'value']);
    }

    function it_sends_post_request_to_the_given_url_with_a_given_body_with_given_files($httpClient, Request $request, PostBodyInterface $postbody, $postFileFactory, PostFileInterface $file1, PostFileInterface $file2)
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
}
