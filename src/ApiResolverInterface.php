<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Api;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface ApiResolverInterface
{
    /**
     * @param  ClientInterface $client
     * @param  string          $resource
     * @return ApiInterface
     */
    public function resolve(ClientInterface $client, $resource);
}
