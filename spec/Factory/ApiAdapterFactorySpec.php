<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Api\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Api\AdapterInterface;
use Sylius\Api\ApiInterface;
use Sylius\Api\Factory\AdapterFactoryInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class ApiAdapterFactorySpec extends ObjectBehavior
{
    function let(ApiInterface $api)
    {
        $this->beConstructedWith($api);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Api\Factory\ApiAdapterFactory');
    }

    function it_implements_adapter_interface()
    {
        $this->shouldImplement(AdapterFactoryInterface::class);
    }

    function it_returns_api_adapter_interface()
    {
        $this->create()->shouldImplement(AdapterInterface::class);
    }
}
