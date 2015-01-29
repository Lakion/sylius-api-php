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

use GuzzleHttp\Post\PostFile;

/**
 * Factory used to create post file
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class PostFileFactory implements PostFileFactoryInterface
{
    /**
     * {@inheritdoc }
     */
    public function create($key, $filePath)
    {
        return new PostFile($key, fopen($filePath, 'r'));
    }
}
