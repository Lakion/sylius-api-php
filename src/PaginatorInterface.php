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
interface PaginatorInterface
{
    /**
     * @return int
     */
    public function getNumberOfResults();

    /**
     * @return array
     */
    public function getCurrentPageResults();

    /**
     * @return int
     */
    public function getCurrentPage();

    /**
     * Moves to the next page
     */
    public function nextPage();

    /**
     * @return bool
     */
    public function hasNextPage();

    /**
     * Moves to the previous page
     */
    public function previousPage();

    /**
     * @return bool
     */
    public function hasPreviousPage();
}
