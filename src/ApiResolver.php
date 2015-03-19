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
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client, UriMapInterface $uriMap)
    {
        $this->client = $client;
        $this->uriMap = $uriMap;
    }

    /**
     * {@inheritdoc}
     */
    public function getApi($resource)
    {
        return new GenericApi($this->client, $this->uriMap->getUri($resource));
    }
}
