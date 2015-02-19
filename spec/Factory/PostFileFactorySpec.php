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

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class PostFileFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Api\Factory\PostFileFactory');
    }

    function it_implements_paginator_factory_interface()
    {
        $this->shouldImplement('Sylius\Api\Factory\PostFileFactoryInterface');
    }
}
