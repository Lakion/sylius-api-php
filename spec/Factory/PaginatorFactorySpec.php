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

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class PaginatorFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Api\Factory\PaginatorFactory');
    }

    function it_implements_paginator_factory_interface()
    {
        $this->shouldImplement('Sylius\Api\Factory\PaginatorFactoryInterface');
    }

    function it_returns_api_adapter_interface(AdapterInterface $adapter)
    {
        $this->create($adapter, 10)->shouldImplement('Sylius\Api\PaginatorInterface');
    }
}
