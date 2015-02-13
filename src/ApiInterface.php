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
interface ApiInterface
{
    /**
     * @param  string|int $id Resource ID
     * @return array
     */
    public function get($id);

    /**
     * @return array
     */
    public function getAll();

    /**
     * @param  int $page
     * @param  int $limit
     * @return array
     */
    public function getPaginated($page = 1, $limit = 10);

    /**
     * @param  int $limit
     * @return PaginatorInterface
     */
    public function createPaginator($limit);

    /**
     * @param  array $body  Array of fields to be sent to api
     * @param  array $files Array of files to upload. Key = field key, Value = file path.
     * @return array
     */
    public function create(array $body, array $files = []);

    /**
     * @param  int   $id    Resource ID
     * @param  array $body  Array of fields to be sent to api
     * @param  array $files Array of files to upload. Key = field key, Value = file path.
     * @return bool
     */
    public function update($id, array $body, array $files = []);

    /**
     * @param  string|int $id Resource ID
     * @return bool
     */
    public function delete($id);
}
