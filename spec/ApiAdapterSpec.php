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
        $this->shouldImplement('Sylius\Api\AdapterInterface');
    }

    function it_gets_number_of_results($api)
    {
        $api->getPaginated()->willReturn(
            array(
                'page' => 1,
                'limit' => 10,
                'pages' => 1,
                'total' => 3,
                '_embedded' =>
                    array(
                        'items' =>
                            array (
                                0 =>
                                    array(
                                        'id' => 1,
                                        'email' => 'chelsie.witting@example.com',
                                        'username' => 'chelsie.witting@example.com',
                                    ),
                                1 =>
                                    array(
                                        'id' => 2,
                                        'email' => 'chelsie.witting1@example.com',
                                        'username' => 'chelsie.witting1@example.com',
                                    ),
                                2 =>
                                    array(
                                        'id' => 3,
                                        'email' => 'chelsie.witting2@example.com',
                                        'username' => 'chelsie.witting2@example.com',
                                    ),
                            ),
                    ),
            )
        );
        $this->getNumberOfResults()->shouldReturn(3);
    }

    function it_gets_results($api)
    {
        $api->getPaginated(1, 10)->willReturn(
            array(
                'page' => 1,
                'limit' => 10,
                'pages' => 1,
                'total' => 3,
                '_embedded' =>
                    array(
                        'items' =>
                            array (
                                0 =>
                                    array(
                                        'id' => 1,
                                        'email' => 'chelsie.witting@example.com',
                                        'username' => 'chelsie.witting@example.com',
                                    ),
                                1 =>
                                    array(
                                        'id' => 2,
                                        'email' => 'chelsie.witting1@example.com',
                                        'username' => 'chelsie.witting1@example.com',
                                    ),
                                2 =>
                                    array(
                                        'id' => 3,
                                        'email' => 'chelsie.witting2@example.com',
                                        'username' => 'chelsie.witting2@example.com',
                                    ),
                            ),
                    ),
            )
        );
        $this->getResults(1, 10)->shouldReturn(
            array (
                    array(
                        'id' => 1,
                        'email' => 'chelsie.witting@example.com',
                        'username' => 'chelsie.witting@example.com',
                    ),
                    array(
                        'id' => 2,
                        'email' => 'chelsie.witting1@example.com',
                        'username' => 'chelsie.witting1@example.com',
                    ),
                    array(
                        'id' => 3,
                        'email' => 'chelsie.witting2@example.com',
                        'username' => 'chelsie.witting2@example.com',
                    ),
            )
        );
    }
}
