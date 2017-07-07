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
use Prophecy\Argument;
use Sylius\Api\AdapterInterface;
use Sylius\Api\ApiInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class ApiAdapterSpec extends ObjectBehavior
{
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

    function it_gets_number_of_results(ApiInterface $api, Promise $promise)
    {
        $response = [
            'page' => 1,
            'limit' => 2,
            'pages' => 2,
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
                ],
            ],
        ];

        $api->getPaginatedAsync(['page' => 1, 'limit' => 2], [])->willReturn($promise);
        $promise->wait()->shouldBeCalled()->willReturn($response);

        $this->getNumberOfResults(['page' => 1, 'limit' => 2])->shouldReturn(3);
    }

    function it_caches_results_on_get_number_of_results(ApiInterface $api, Promise $promise)
    {
        $response = [
            'page' => 1,
            'limit' => 2,
            'pages' => 2,
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
                ],
            ],
        ];

        $api->getPaginatedAsync(['page' => 1, 'limit' => 2], [])->willReturn($promise)->shouldBeCalledTimes(1);
        $promise->wait()->shouldBeCalled()->willReturn($response['_embedded']['items']);
        $promise->then(Argument::type('callable'))->shouldBeCalled()->willReturn($promise);

        $this->getNumberOfResults(['page' => 1, 'limit' => 2]);
        $this->getNumberOfResults(['page' => 1, 'limit' => 2]);

        $this->getResults(['page' => 1, 'limit' => 2])->shouldReturn([
            [
                'id' => 1,
                'email' => 'chelsie.witting@example.com',
                'username' => 'chelsie.witting@example.com',
            ],
            [
                'id' => 2,
                'email' => 'chelsie.witting1@example.com',
                'username' => 'chelsie.witting1@example.com',
            ],
        ]);
    }

    function it_gets_fresh_results_on_different_parameters_on_get_number_of_results(
        ApiInterface $api,
        Promise $promiseOne,
        Promise $promiseTwo
    ) {
        $responseOne = [
            'page' => 1,
            'limit' => 2,
            'pages' => 2,
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
                ],
            ],
        ];

        $responseTwo = [
            'page' => 2,
            'limit' => 2,
            'pages' => 2,
            'total' => 3,
            '_embedded' => [
                'items' => [
                    0 => [
                        'id' => 3,
                        'email' => 'chelsie.witting2@example.com',
                        'username' => 'chelsie.witting2@example.com',
                    ],
                ],
            ],
        ];

        $api->getPaginatedAsync(['page' => 1, 'limit' => 2], [])->willReturn($promiseOne);
        $api->getPaginatedAsync(['page' => 2, 'limit' => 2], [])->willReturn($promiseTwo)->shouldBeCalledTimes(1);

        $promiseTwo->wait()->shouldBeCalled()->willReturn($responseTwo['_embedded']['items']);
        $promiseTwo->then(Argument::type('callable'))->shouldBeCalled()->willReturn($promiseTwo);

        $this->getNumberOfResults(['page' => 1, 'limit' => 2]);
        $this->getNumberOfResults(['page' => 2, 'limit' => 2]);

        $this->getResults(['page' => 2, 'limit' => 2])->shouldReturn([
            [
                'id' => 3,
                'email' => 'chelsie.witting2@example.com',
                'username' => 'chelsie.witting2@example.com',
            ],
        ]);
    }

    function it_returns_fresh_results_on_get_number_of_results(
        ApiInterface $api,
        Promise $promiseOne,
        Promise $promiseTwo
    ) {
        $responseOne = [
            'page' => 1,
            'limit' => 2,
            'pages' => 2,
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
                ],
            ],
        ];

        $responseTwo = [
            'page' => 2,
            'limit' => 2,
            'pages' => 2,
            'total' => 3,
            '_embedded' => [
                'items' => [
                    0 => [
                        'id' => 3,
                        'email' => 'chelsie.witting2@example.com',
                        'username' => 'chelsie.witting2@example.com',
                    ],
                ],
            ],
        ];

        $api->getPaginatedAsync(['page' => 1, 'limit' => 2], [])->willReturn($promiseOne);
        $api->getPaginatedAsync(['page' => 2, 'limit' => 2], [])->willReturn($promiseTwo);

        $promiseTwo->wait()->shouldBeCalled()->willReturn($responseTwo['_embedded']['items']);
        $promiseTwo->then(Argument::type('callable'))->shouldBeCalled()->willReturn($promiseTwo);

        $this->getNumberOfResults(['page' => 1, 'limit' => 2]);

        $this->getResults(['page' => 2, 'limit' => 2])->shouldReturn([
            [
                'id' => 3,
                'email' => 'chelsie.witting2@example.com',
                'username' => 'chelsie.witting2@example.com',
            ],
        ]);
    }

    function it_gets_number_of_results_for_a_specific_uri_parameters(
        ApiInterface $api,
        Promise $promise
    ) {
        $response = [
            'page' => 1,
            'limit' => 2,
            'pages' => 2,
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
                ],
            ],
        ];

        $api->getPaginatedAsync(['page' => 1, 'limit' => 2], ['parentId' => 1])->willReturn($promise);

        $promise->wait()->shouldBeCalled()->willReturn($response);

        $this->getNumberOfResults(['page' => 1, 'limit' => 2], ['parentId' => 1])->shouldReturn(3);
    }

    function it_gets_results_async(ApiInterface $api, Promise $promise)
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

        $api->getPaginatedAsync(['page' => 1, 'limit' => 10], [])->willReturn($promise);

        $promise->then(Argument::type('callable'))->shouldBeCalled()->willReturn($promise);

        $this->getResultsAsync(['page' => 1, 'limit' => 10])->shouldReturn($promise);
    }

    function it_gets_results(ApiInterface $api, Promise $promise)
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

        $api->getPaginatedAsync(['page' => 1, 'limit' => 10], [])->willReturn($promise);

        $promise->wait()->shouldBeCalled()->willReturn($response['_embedded']['items']);
        $promise->then(Argument::type('callable'))->shouldBeCalled()->willReturn($promise);

        $this->getResults(['page' => 1, 'limit' => 10])->shouldReturn(
            [
                [
                    'id' => 1,
                    'email' => 'chelsie.witting@example.com',
                    'username' => 'chelsie.witting@example.com',
                ],
                [
                    'id' => 2,
                    'email' => 'chelsie.witting1@example.com',
                    'username' => 'chelsie.witting1@example.com',
                ],
                [
                    'id' => 3,
                    'email' => 'chelsie.witting2@example.com',
                    'username' => 'chelsie.witting2@example.com',
                ],
            ]
        );
    }

    function it_gets_results_for_a_specific_uri_parameters(ApiInterface $api, Promise $promise)
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

        $api->getPaginatedAsync(['page' => 1, 'limit' => 10], ['parentId' => 1])->willReturn($promise);

        $promise->wait()->shouldBeCalled()->willReturn($response['_embedded']['items']);
        $promise->then(Argument::type('callable'))->shouldBeCalled()->willReturn($promise);

        $this->getResults(['page' => 1, 'limit' => 10], ['parentId' => 1])->shouldReturn(
            [
                [
                    'id' => 1,
                    'email' => 'chelsie.witting@example.com',
                    'username' => 'chelsie.witting@example.com',
                ],
                [
                    'id' => 2,
                    'email' => 'chelsie.witting1@example.com',
                    'username' => 'chelsie.witting1@example.com',
                ],
                [
                    'id' => 3,
                    'email' => 'chelsie.witting2@example.com',
                    'username' => 'chelsie.witting2@example.com',
                ],
            ]
        );
    }
}
