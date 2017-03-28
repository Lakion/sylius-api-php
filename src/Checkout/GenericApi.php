<?php

namespace Sylius\Api\Checkout;

use Sylius\Api\GenericApi as BaseApi;

class GenericApi extends BaseApi
{
    public function create(array $body, array $uriParameters = [], array $files = [])
    {
        $response = $this
            ->getClient()
            ->put(
                $this->getUri(
                    $uriParameters,
                    $body,
                    $files
                ),
                $body
            )
        ;

        return $response->getStatusCode() === 204;
    }

    public function update($id, array $body, array $uriParameters = [], array $files = [])
    {
        $uri = sprintf('%s%s', $this->getUri($uriParameters), $id);

        $response = $this
            ->getClient()
            ->put(
                $uri,
                $body
            )
        ;

        return $response->getStatusCode() === 204;
    }
}
