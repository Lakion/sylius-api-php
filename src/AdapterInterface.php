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
interface AdapterInterface
{
    /**
     * @param array $uriParameters
     * @return int
     */
    public function getNumberOfResults(array $uriParameters = []);

    /**
     * @param int    $page
     * @param int    $limit
     * @param array  $uriParameters
     * @return array
     */
    public function getResults($page, $limit, array $uriParameters = []);
}
