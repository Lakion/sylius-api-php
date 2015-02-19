<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Api\Map;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class ArrayUriMap implements UriMapInterface
{
    /**
     * @var array $uriMapping
     */
    private $uriMapping;
    /**
     * @var bool $allowDefaultUris
     */
    private $allowDefaultUris;

    /**
     * @param array $uriMapping
     * @param bool $allowDefaultUris
     */
    public function __construct(array $uriMapping, $allowDefaultUris = true)
    {
        $this->uriMapping = $uriMapping;
        $this->allowDefaultUris = $allowDefaultUris;
    }

    /**
     * {@inheritdoc}
     */
    public function getUri($resource)
    {
        if (empty($resource) || !is_string($resource)) {
            throw new \InvalidArgumentException('The resource has to be string and cannot be empty.');
        }
        if(isset($this->uriMapping[$resource])) {
            return $this->uriMapping[$resource];
        }
        if ($this->allowDefaultUris) {
            return $resource;
        }
        throw new \InvalidArgumentException('No mapping defined for a given resource.');
    }
}
