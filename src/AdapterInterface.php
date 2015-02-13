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
     * @return int
     */
    public function getNumberOfResults();

    /**
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getResults($page, $limit);
}
