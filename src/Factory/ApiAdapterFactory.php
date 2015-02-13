<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Api\Factory;

use Sylius\Api\AdapterInterface;
use Sylius\Api\ApiAdapter;
use Sylius\Api\ApiInterface;
use Sylius\Api\Paginator;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class ApiAdapterFactory implements AdapterFactoryInterface
{
    /**
     * @var ApiInterface $api
     */
    private $api;

    public function __construct(ApiInterface $api)
    {
        $this->api = $api;
    }

    /**
     * {@inheritdoc }
     */
    public function create()
    {
        return new ApiAdapter($this->api);
    }
}
