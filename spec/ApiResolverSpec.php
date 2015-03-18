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

use PhpSpec\ObjectBehavior;
use Sylius\Api\ApiInterface;
use Sylius\Api\ClientInterface;
use Sylius\Api\Map\UriMapInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class ApiResolverSpec extends ObjectBehavior
{
    function let(ClientInterface $client, UriMapInterface $uriMap)
    {
        $uriMap->getUri('products')->willReturn('products');
        $this->beConstructedWith($client, $uriMap);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Api\ApiResolver');
    }

    function it_should_implement_api_resolver_interface()
    {
        $this->shouldImplement('Sylius\Api\ApiResolverInterface');
    }

    function its_resolve_function_should_return_api_interface_instance()
    {
        $this->getApi('products')->shouldReturnAnInstanceOf('Sylius\Api\ApiInterface');
    }

    function it_should_use_uri_map_to_resolve_api($uriMap)
    {
        $uriMap->getUri('products')->shouldBeCalled();
        $this->getApi('products')->shouldReturnAnInstanceOf('Sylius\Api\ApiInterface');
    }
}
