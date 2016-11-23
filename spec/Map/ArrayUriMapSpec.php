<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Api\Map;

use PhpSpec\ObjectBehavior;
use Sylius\Api\Map\UriMapInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class ArrayUriMapSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            [
                'product_variants' => 'products/{productId}/products_variants',
                'taxons' => 'taxonomies/{taxonomyId}/taxons',
            ]
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Api\Map\ArrayUriMap');
    }

    function it_implements_uri_map_interface()
    {
        $this->shouldImplement(UriMapInterface::class);
    }

    function its_get_uri_method_accepts_only_string()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('getUri', [123]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('getUri', [1.23]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('getUri', [true]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('getUri', [[]]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('getUri', [new \stdClass()]);
        $this->shouldNotThrow(\InvalidArgumentException::class)->during('getUri', ['string']);
    }

    function it_throws_exception_when_empty_string_given_to_get_uri_method()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('getUri', ['']);
    }

    function it_gets_uri_for_defined_resource()
    {
        $this->getUri('product_variants')->shouldReturn('products/{productId}/products_variants');
        $this->getUri('taxons')->shouldReturn('taxonomies/{taxonomyId}/taxons');
    }

    function it_gets_default_uri_for_undefined_resource()
    {
        $this->getUri('products')->shouldReturn('products');
        $this->getUri('taxonomies')->shouldReturn('taxonomies');
    }

    function it_throws_exception_when_uri_for_resource_is_undefined_and_allow_default_uri_option_is_false()
    {
        $this->beConstructedWith(
            [
                'product_variants' => 'products/{productId}/products_variants',
                'taxons' => 'taxonomies/{taxonomyId}/taxons',
            ],
            false
        );
        $this->getUri('taxons')->shouldReturn('taxonomies/{taxonomyId}/taxons');
        $this->shouldThrow(\InvalidArgumentException::class)->during('getUri', ['products']);
    }
}
