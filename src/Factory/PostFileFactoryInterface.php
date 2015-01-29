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

use GuzzleHttp\Post\PostFileInterface;

/**
 * Post file factory interface
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface PostFileFactoryInterface
{
    /**
     * @param  string            $key
     * @param  string            $filePath
     * @return PostFileInterface
     */
    public function create($key, $filePath);
}
