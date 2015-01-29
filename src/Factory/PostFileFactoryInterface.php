<?php

namespace Sylius\Api\Factory;

use GuzzleHttp\Post\PostFileInterface;

interface PostFileFactoryInterface
{
    /**
     * @param  string            $key
     * @param  string            $filePath
     * @return PostFileInterface
     */
    public function create($key, $filePath);
}
