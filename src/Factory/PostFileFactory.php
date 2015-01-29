<?php

namespace Sylius\Api\Factory;

use GuzzleHttp\Post\PostFile;
use GuzzleHttp\Post\PostFileInterface;

class PostFileFactory
{
    /**
     * @param  string            $key
     * @param  string            $filePath
     * @return PostFileInterface
     */
    public function create($key, $filePath)
    {
        return new PostFile($key, fopen($filePath, 'r'));
    }
}
