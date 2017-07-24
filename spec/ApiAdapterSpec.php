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
use GuzzleHttp\Promise\PromiseInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Api\AdapterInterface;
use Sylius\Api\ApiInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class ApiAdapterSpec extends ObjectBehavior
{
    private static function promisedResponse(array $response)
    {
        $promise = new Promise(function () use (&$promise, $response) {
            $promise->resolve($response);
        });

        return $promise;
    }

    function let(ApiInterface $api)
    {
        $this->beConstructedWith($api);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Api\ApiAdapter');
    }

    function it_implements_adapter_interface()
    {
        $this->shouldImplement(AdapterInterface::class);
    }

    function it_gets_results_async(ApiInterface $api)
    {
        $response = [
            'page' => 1,
            'limit' => 10,
            'pages' => 1,
            'total' => 3,
            '_embedded' => [
                'items' => [
                    0 => [
                        'id' => 1,
                        'email' => 'chelsie.witting@example.com',
                        'username' => 'chelsie.witting@example.com',
                    ],
                    1 => [
                        'id' => 2,
                        'email' => 'chelsie.witting1@example.com',
                        'username' => 'chelsie.witting1@example.com',
                    ],
                    2 => [
                        'id' => 3,
                        'email' => 'chelsie.witting2@example.com',
                        'username' => 'chelsie.witting2@example.com',
                    ],
                ],
            ],
        ];

        $promise = self::promisedResponse($response);

        $api->getPaginatedAsync(['page' => 1, 'limit' => 10], [])->willReturn($promise);

        $this->getResultsAsync(['page' => 1, 'limit' => 10])->shouldHaveType(PromiseInterface::class);
    }

    function it_gets_results(ApiInterface $api)
    {
        $response = [
            'page' => 1,
            'limit' => 10,
            'pages' => 1,
            'total' => 3,
            '_embedded' => [
                'items' => [
                    0 => [
                        'id' => 1,
                        'email' => 'chelsie.witting@example.com',
                        'username' => 'chelsie.witting@example.com',
                    ],
                    1 => [
                        'id' => 2,
                        'email' => 'chelsie.witting1@example.com',
                        'username' => 'chelsie.witting1@example.com',
                    ],
                    2 => [
                        'id' => 3,
                        'email' => 'chelsie.witting2@example.com',
                        'username' => 'chelsie.witting2@example.com',
                    ],
                ],
            ],
        ];

        $promise = self::promisedResponse($response);

        $api->getPaginatedAsync(['page' => 1, 'limit' => 10], [])->willReturn($promise);

        $this->getResults(['page' => 1, 'limit' => 10])->shouldReturn($response);
    }

    function it_gets_results_for_a_specific_uri_parameters(ApiInterface $api)
    {
        $response = [
            'page' => 1,
            'limit' => 10,
            'pages' => 1,
            'total' => 3,
            '_embedded' => [
                'items' => [
                    0 => [
                        'id' => 1,
                        'email' => 'chelsie.witting@example.com',
                        'username' => 'chelsie.witting@example.com',
                    ],
                    1 => [
                        'id' => 2,
                        'email' => 'chelsie.witting1@example.com',
                        'username' => 'chelsie.witting1@example.com',
                    ],
                    2 => [
                        'id' => 3,
                        'email' => 'chelsie.witting2@example.com',
                        'username' => 'chelsie.witting2@example.com',
                    ],
                ],
            ],
        ];

        $promise = self::promisedResponse($response);

        $api->getPaginatedAsync(['page' => 1, 'limit' => 10], ['parentId' => 1])->willReturn($promise);

        $this->getResults(['page' => 1, 'limit' => 10], ['parentId' => 1])->shouldReturn($response);
    }
}
