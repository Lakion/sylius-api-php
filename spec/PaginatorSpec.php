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

use GuzzleHttp\Promise\Promise;
use PhpSpec\ObjectBehavior;
use Sylius\Api\AdapterInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class PaginatorSpec extends ObjectBehavior
{
    function let(AdapterInterface $adapter)
    {
        $this->beConstructedWith($adapter, ['limit' => 10], []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Api\Paginator');
    }

    function it_implements_paginator_interface()
    {
        $this->shouldImplement('Sylius\Api\PaginatorInterface');
    }

    function it_has_limit_10_by_default(AdapterInterface $adapter, Promise $promise)
    {
        $this->beConstructedWith($adapter);

        $return = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'];

        $adapter->getNumberOfResults(['page' => 1, 'limit' => 10], [])->willReturn(20);
        $adapter->getResultsAsync(['page' => 1, 'limit' => 10], [])->willReturn($promise);

        $promise->wait()->shouldBeCalled()->willReturn($return);

        $this->getCurrentPageResults()->shouldHaveCount(10);
    }

    function its_limit_can_be_specified(AdapterInterface $adapter, Promise $promise)
    {
        $this->beConstructedWith($adapter, ['limit' => 15]);

        $return = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o'];

        $adapter->getNumberOfResults(['page' => 1, 'limit' => 15], [])->willReturn(30);
        $adapter->getResultsAsync(['page' => 1, 'limit' => 15], [])->willReturn($promise);

        $promise->wait()->shouldBeCalled()->willReturn($return);

        $this->getCurrentPageResults()->shouldHaveCount(15);
    }

    function its_page_can_be_specified(AdapterInterface $adapter)
    {
        $this->beConstructedWith($adapter, ['page' => 2]);
        $adapter->getNumberOfResults(['page' => 2, 'limit' => 10], [])->willReturn(30);
        $adapter->getResults(['page' => 2, 'limit' => 10], [])
            ->willReturn(array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o'));

        $this->getCurrentPage()->shouldEqual(2);
    }

    function it_validates_that_limit_is_int(AdapterInterface $adapter)
    {
        $this->shouldThrow('InvalidArgumentException')->during('__construct', [$adapter, ['limit' => '1']]);
        $this->shouldThrow('InvalidArgumentException')->during('__construct', [$adapter, ['limit' => new \stdClass()]]);
        $this->shouldThrow('InvalidArgumentException')->during('__construct', [$adapter, ['limit' => 1.5]]);
    }

    function it_validates_that_page_is_int($adapter)
    {
        $this->shouldThrow('InvalidArgumentException')->during('__construct', [$adapter, ['page' => '1']]);
        $this->shouldThrow('InvalidArgumentException')->during('__construct', [$adapter, ['page' => new \stdClass()]]);
        $this->shouldThrow('InvalidArgumentException')->during('__construct', [$adapter, ['page' => 1.5]]);
    }

    function it_gets_current_page_results(AdapterInterface $adapter, Promise $promise)
    {
        $return = ['a', 'b', 'c'];

        $adapter->getNumberOfResults(['page' => 1, 'limit' => 10], [])->willReturn(3);
        $adapter->getResultsAsync(['page' => 1, 'limit' => 10], [])->willReturn($promise);

        $promise->wait()->shouldBeCalled()->willReturn($return);

        $this->getCurrentPageResults()->shouldReturn(['a', 'b', 'c']);
    }

    function it_caches_results_for_current_page(AdapterInterface $adapter, Promise $promiseOne, Promise $promiseTwo)
    {
        $returnOne = ['a', 'b', 'c'];
        $adapter->getNumberOfResults(['page' => 1, 'limit' => 10],[])->willReturn(3);
        $adapter->getResultsAsync(['page' => 1, 'limit' => 10], [])->shouldBeCalledTimes(1);
        $adapter->getResultsAsync(['page' => 1, 'limit' => 10], [])->willReturn($promiseOne);
        $promiseOne->wait()->shouldBeCalled()->willReturn($returnOne);
        $this->getCurrentPageResults()->shouldReturn(['a', 'b', 'c']);

        $returnTwo = ['d', 'e', 'f'];
        $adapter->getResultsAsync(['page' => 1, 'limit' => 10], [])->willReturn($promiseTwo);
        $this->getCurrentPageResults()->shouldReturn(['a', 'b', 'c']);
    }

    function it_moves_to_the_next_page(AdapterInterface $adapter, Promise $promiseOne, Promise $promiseTwo)
    {
        $this->beConstructedWith($adapter, ['limit' => 5]);

        $adapter->getNumberOfResults(['page' => 1, 'limit' => 5], [])->willReturn(8);

        $adapter->getResultsAsync(['page' => 1, 'limit' => 5], [])->willReturn($promiseOne);
        $promiseOne->wait()->shouldBeCalled()->willReturn(['a', 'b', 'c', 'b', 'e']);

        $adapter->getResultsAsync(['page' => 2, 'limit' => 5], [])->willReturn($promiseTwo);
        $promiseTwo->wait()->shouldBeCalled()->willReturn(['f', 'g', 'h']);

        $this->getCurrentPageResults()->shouldReturn(['a', 'b', 'c', 'b', 'e']);
        $this->nextPage();
        $this->getCurrentPageResults()->shouldReturn(['f', 'g', 'h']);
    }

    function it_moves_to_the_previous_page(AdapterInterface $adapter, Promise $promiseOne, Promise $promiseTwo)
    {
        $this->beConstructedWith($adapter, ['limit' => 5]);

        $adapter->getNumberOfResults(['page' => 1, 'limit' => 5], [])->willReturn(8);
        $adapter->getResultsAsync(['page' => 1, 'limit' => 5], [])->shouldBeCalledTimes(2);

        $adapter->getResultsAsync(['page' => 1, 'limit' => 5], [])->willReturn($promiseOne);
        $promiseOne->wait()->shouldBeCalled()->willReturn(['a', 'b', 'c', 'b', 'e']);

        $adapter->getResultsAsync(['page' => 2, 'limit' => 5], [])->willReturn($promiseTwo);
        $promiseTwo->wait()->shouldBeCalled()->willReturn(['f', 'g', 'h']);

        $this->getCurrentPageResults()->shouldReturn(['a', 'b', 'c', 'b', 'e']);
        $this->nextPage();
        $this->getCurrentPageResults()->shouldReturn(['f', 'g', 'h']);
        $this->previousPage();
        $this->getCurrentPageResults()->shouldReturn(['a', 'b', 'c', 'b', 'e']);
    }

    function it_returns_false_if_there_is_no_previous_page()
    {
        $this->hasPreviousPage()->shouldReturn(false);
    }

    function it_throws_exception_when_can_not_move_to_the_previous_page()
    {
        $this->shouldThrow('LogicException')->during('previousPage', []);
    }

    function it_returns_true_if_there_is_previous_page(AdapterInterface $adapter)
    {
        $adapter->getNumberOfResults(['page' => 1, 'limit' => 10], [])->willReturn(25);

        $this->nextPage();
        $this->hasPreviousPage()->shouldReturn(true);
    }

    function it_gets_number_of_results(AdapterInterface $adapter)
    {
        $adapter->getNumberOfResults(['page' => 1, 'limit' => 10], [])->willReturn(5);

        $this->getNumberOfResults()->shouldReturn(5);
    }

    function it_returns_false_if_there_is_no_next_page(AdapterInterface $adapter)
    {
        $adapter->getNumberOfResults(['page' => 1, 'limit' => 10], [])->willReturn(8);

        $this->hasNextPage()->shouldReturn(false);
    }

    function it_returns_true_if_there_is_next_page(AdapterInterface $adapter)
    {
        $adapter->getNumberOfResults(['page' => 1, 'limit' => 10], [])->willReturn(25);

        $this->hasNextPage()->shouldReturn(true);
    }

    function it_throws_exception_when_can_not_move_to_the_next_page(AdapterInterface $adapter)
    {
        $adapter->getNumberOfResults(['page' => 1, 'limit' => 10], [])->willReturn(8);

        $this->shouldThrow('LogicException')->during('nextPage', []);
    }
}
