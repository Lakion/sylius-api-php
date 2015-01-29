<?php

namespace Sylius\Api\Factory;

use GuzzleHttp\Post\PostFile;

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
