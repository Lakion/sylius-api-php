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
     * @param  string|int $id            Resource ID
     * @param  array      $uriParameters
     * @return array
     */
    public function get($id, array $uriParameters = []);

    /**
     * @param  array $queryParameters
     * @param  array $uriParameters
     * @return array
     */
    public function getAll(array $queryParameters = [], array $uriParameters = []);

    /**
     * @param  array $queryParameters
     * @param  array $uriParameters
     * @return array
     */
    public function getPaginated(array $queryParameters = [], array $uriParameters = []);

    /**
     * @param  array              $queryParameters
     * @param  array              $uriParameters
     * @return PaginatorInterface
     */
    public function createPaginator(array $queryParameters = [], array $uriParameters = []);

    /**
     * @param  array $body          Array of fields to be sent to api
     * @param  array $uriParameters
     * @param  array $files         Array of files to upload. Key = field key, Value = file path.
     * @return array
     */
    public function create(array $body, array $uriParameters = [], array $files = []);

    /**
     * @param  int   $id            Resource ID
     * @param  array $body          Array of fields to be sent to api
     * @param  array $uriParameters
     * @param  array $files         Array of files to upload. Key = field key, Value = file path.
     * @return bool
     */
    public function update($id, array $body, array $uriParameters = [], array $files = []);

    /**
     * @param  string|int $id            Resource ID
     * @param  array      $uriParameters
     * @return bool
     */
    public function delete($id, array $uriParameters = []);
}
