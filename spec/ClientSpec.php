<?php

namespace spec\Sylius\Api;

use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Post\PostBodyInterface;
use GuzzleHttp\Post\PostFileInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ClientSpec extends ObjectBehavior
{
    function let(HttpClientInterface $httpClient)
    {
        $this->beConstructedWith($httpClient);
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
