<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Api\Map;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */

interface UriMapInterface
{
    /**
     * @param string $resource
     * @return string Uri for given resource
     */
    public function getUri($resource);
}
