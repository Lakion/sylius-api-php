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
    private static function promisedResponse(array $items, array $options = [])
    {
        $return = \array_merge([
            'total' => count($items),
            '_embedded' => [
                'items' => $items,
            ],
        ], $options);

        $promise = new Promise(function () use (&$promise, $return) {
            $promise->resolve($return);
        });

        return $promise;
    }

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

    function it_has_limit_10_by_default(AdapterInterface $adapter)
    {
        $return = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'];
        $promise = self::promisedResponse($return);

        $this->beConstructedWith($adapter);

        $adapter->getResultsAsync(['page' => 1, 'limit' => 10], [])->willReturn($promise);

        $this->getCurrentPageResults()->shouldHaveCount(10);
    }

    function its_limit_can_be_specified(AdapterInterface $adapter, Promise $promise)
    {
        $return = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o'];
        $promise = self::promisedResponse($return);

        $this->beConstructedWith($adapter, ['limit' => 15]);

        $adapter->getResultsAsync(['page' => 1, 'limit' => 15], [])->willReturn($promise);

        $this->getCurrentPageResults()->shouldHaveCount(15);
    }

    function its_page_can_be_specified(AdapterInterface $adapter)
    {
        $return = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o'];

        $this->beConstructedWith($adapter, ['page' => 2]);

        $adapter->getResults(['page' => 2, 'limit' => 10], [])->willReturn($return);

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

    function it_gets_current_page_results(AdapterInterface $adapter)
    {
        $return = ['a', 'b', 'c'];
        $promise = self::promisedResponse($return);

        $adapter->getResultsAsync(['page' => 1, 'limit' => 10], [])->willReturn($promise);

        $this->getCurrentPageResults()->shouldReturn(['a', 'b', 'c']);
    }

    function it_caches_results_for_current_page(AdapterInterface $adapter)
    {
        $returnOne = ['a', 'b', 'c'];
        $promiseOne = self::promisedResponse($returnOne);
        $adapter->getResultsAsync(['page' => 1, 'limit' => 10], [])->shouldBeCalledTimes(1);
        $adapter->getResultsAsync(['page' => 1, 'limit' => 10], [])->willReturn($promiseOne);
        $this->getCurrentPageResults()->shouldReturn(['a', 'b', 'c']);

        $returnTwo = ['d', 'e', 'f'];
        $promiseTwo = self::promisedResponse($returnTwo);
        $adapter->getResultsAsync(['page' => 1, 'limit' => 10], [])->willReturn($promiseTwo);
        $this->getCurrentPageResults()->shouldReturn(['a', 'b', 'c']);
    }

    function it_moves_to_the_next_page(AdapterInterface $adapter)
    {
        $this->beConstructedWith($adapter, ['limit' => 5]);

        $returnOne = ['a', 'b', 'c', 'b', 'e'];
        $promiseOne = self::promisedResponse($returnOne, ['total' => 8]);
        $adapter->getResultsAsync(['page' => 1, 'limit' => 5], [])->willReturn($promiseOne);

        $returnTwo = ['f', 'g', 'h'];
        $promiseTwo = self::promisedResponse($returnTwo);
        $adapter->getResultsAsync(['page' => 2, 'limit' => 5], [])->willReturn($promiseTwo);

        $this->getCurrentPageResults()->shouldReturn(['a', 'b', 'c', 'b', 'e']);
        $this->nextPage();
        $this->getCurrentPageResults()->shouldReturn(['f', 'g', 'h']);
    }

    function it_moves_to_the_previous_page(AdapterInterface $adapter)
    {
        $this->beConstructedWith($adapter, ['limit' => 5]);

        $returnOne = ['a', 'b', 'c', 'b', 'e'];
        $promiseOne = self::promisedResponse($returnOne, ['total' => 8]);
        $adapter->getResultsAsync(['page' => 1, 'limit' => 5], [])->shouldBeCalledTimes(2);
        $adapter->getResultsAsync(['page' => 1, 'limit' => 5], [])->willReturn($promiseOne);

        $returnTwo =['f', 'g', 'h'];
        $promiseTwo = self::promisedResponse($returnTwo);
        $adapter->getResultsAsync(['page' => 2, 'limit' => 5], [])->willReturn($promiseTwo);

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
        $this->beConstructedWith($adapter, ['limit' => 2]);

        $promise = self::promisedResponse(['a', 'b'], ['total' => 3]);
        $adapter->getResultsAsync(['page' => 1, 'limit' => 2], [])->willReturn($promise);

        $this->getCurrentPageResults();
        $this->nextPage();
        $this->hasPreviousPage()->shouldReturn(true);
    }

    function it_gets_number_of_results(AdapterInterface $adapter)
    {
        $promise = self::promisedResponse([], ['total' => 3]);
        $adapter->getResultsAsync(['page' => 1, 'limit' => 10], [])->willReturn($promise);

        $this->getCurrentPageResults();
        $this->getNumberOfResults()->shouldReturn(3);
    }

    function it_returns_false_if_there_is_no_next_page(AdapterInterface $adapter)
    {
        $promise = self::promisedResponse([], ['total' => 10]);
        $adapter->getResultsAsync(['page' => 1, 'limit' => 10], [])->willReturn($promise);

        $this->getCurrentPageResults();
        $this->hasNextPage()->shouldReturn(false);
    }

    function it_returns_true_if_there_is_next_page(AdapterInterface $adapter)
    {
        $promise = self::promisedResponse([], ['total' => 15]);
        $adapter->getResultsAsync(['page' => 1, 'limit' => 10], [])->willReturn($promise);

        $this->getCurrentPageResults();
        $this->hasNextPage()->shouldReturn(true);
    }

    function it_throws_exception_when_can_not_move_to_the_next_page(AdapterInterface $adapter)
    {
        $promise = self::promisedResponse([], ['total' => 10]);
        $adapter->getResultsAsync(['page' => 1, 'limit' => 10], [])->willReturn($promise);

        $this->shouldThrow('LogicException')->during('nextPage', []);
    }

    function it_throws_exception_when_checking_next_page_if_no_results_cached(AdapterInterface $adapter)
    {
        $promise = self::promisedResponse([], ['total' => 10]);
        $adapter->getResultsAsync(['page' => 1, 'limit' => 10], [])->willReturn($promise);

        $this->shouldThrow('LogicException')->during('hasNextPage', []);
    }

    function it_throws_exception_when_getting_total_results_if_no_results_cached(AdapterInterface $adapter)
    {
        $promise = self::promisedResponse([], ['total' => 10]);
        $adapter->getResultsAsync(['page' => 1, 'limit' => 10], [])->willReturn($promise);

        $this->shouldThrow('LogicException')->during('getNumberOfResults', []);
    }
}
