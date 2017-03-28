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
                )
            )
        ;

        return $response->getStatusCode() === 204;
    }

    public function update($id, array $body, array $uriParameters = [], array $files = [])
    {
        $uriParameters = array_merge(
            $uriParameters, ['cartId' => $id]
        );

        return $this->create(
            $body,
            $uriParameters,
            $files
        );
    }
}
