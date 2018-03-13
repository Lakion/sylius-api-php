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

use GuzzleHttp\Promise\Promise;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface PaginatorInterface
{
    /**
     * @return int
     *
     * @throws LogicException
     */
    public function getNumberOfResults();

    /**
     * @return array
     */
    public function getCurrentPageResults();

    /**
     * @return Promise
     */
    public function getCurrentPageResultsAsync();

    /**
     * @return int
     */
    public function getCurrentPage();

    /**
     * Moves to the next page
     *
     * @return void
     *
     * @throws LogicException
     */
    public function nextPage();

    /**
     * @return bool
     *
     * @throws LogicException
     */
    public function hasNextPage();

    /**
     * Moves to the previous page
     * @return void
     */
    public function previousPage();

    /**
     * @return bool
     */
    public function hasPreviousPage();
}
