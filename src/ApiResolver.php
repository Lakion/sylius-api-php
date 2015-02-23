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

use Sylius\Api\Map\UriMapInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class ApiResolver implements ApiResolverInterface
{
    /**
     * @var UriMapInterface
     */
    private $uriMap;

    public function __construct(UriMapInterface $uriMap)
    {
        $this->uriMap = $uriMap;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(ClientInterface $client, $resource)
    {
        return new GenericApi($client, $this->uriMap->getUri($resource));
    }
}
