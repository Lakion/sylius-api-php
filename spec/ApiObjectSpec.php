<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Api;

use PhpSpec\ObjectBehavior;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class ApiObjectSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Api\ApiObject');
    }

    function it_should_implement_api_object_interface()
    {
        $this->shouldImplement('Sylius\Api\ApiObjectInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function its_id_can_be_integer()
    {
        $this->setId(1);
        $this->getId()->shouldReturn(1);
    }

    function its_id_can_be_string()
    {
        $this->setId('1');
        $this->getId()->shouldReturn('1');
    }

    function it_has_empty_data_by_default()
    {
        $this->getData()->shouldReturn([]);
    }

    function it_has_no_uri_parameters_by_default()
    {
        $this->getUriParameters()->shouldReturn([]);
    }

    function it_has_no_files_by_default()
    {
        $this->getFiles()->shouldReturn([]);
    }

    function it_should_set_data_through_constructor()
    {
        $this->beConstructedWith(['key' => 'value']);
        $this->getData()->shouldReturn(['key' => 'value']);
    }

    function it_should_set_uri_parameters_through_constructor()
    {
        $this->beConstructedWith([], ['parentId' => 'id']);
        $this->getData()->shouldReturn([]);
        $this->getUriParameters()->shouldReturn(['parentId' => 'id']);
    }

    function it_should_set_files_through_constructor()
    {
        $this->beConstructedWith([], [], ['images[0][file]' => 'filePath']);
        $this->getData()->shouldReturn([]);
        $this->getUriParameters()->shouldReturn([]);
        $this->getFiles()->shouldReturn(['images[0][file]' => 'filePath']);
    }

    function it_should_set_data_field_with_given_key_and_value()
    {
        $this->setDataValue('key', 'value');
        $this->getData()->shouldReturn(['key' => 'value']);
    }

    function it_should_not_override_data_if_different_key_is_set()
    {
        $this->setDataValue('key', 'value');
        $this->getData()->shouldReturn(['key' => 'value']);
        $this->setDataValue('key2', 'value2');
        $this->getData()->shouldReturn(['key' => 'value', 'key2' => 'value2']);
    }

    function it_should_override_data_value_if_key_exists()
    {
        $this->setDataValue('key', 'value');
        $this->getData()->shouldReturn(['key' => 'value']);
        $this->setDataValue('key', 'value2');
        $this->getData()->shouldReturn(['key' => 'value2']);
    }

    function it_should_add_file_with_given_key_and_file_path()
    {
        $this->addFile('images[0][file]', 'filePath');
        $this->getFiles()->shouldReturn(['images[0][file]' => 'filePath']);
    }

    function it_should_add_another_file_if_different_key_is_set()
    {
        $this->addFile('images[0][file]', 'filePath');
        $this->getFiles()->shouldReturn(['images[0][file]' => 'filePath']);
        $this->addFile('images[1][file]', 'filePath2');
        $this->getFiles()->shouldReturn(['images[0][file]' => 'filePath', 'images[1][file]' => 'filePath2']);
    }

    function it_should_override_file_if_given_key_exists()
    {
        $this->addFile('images[0][file]', 'filePath');
        $this->getFiles()->shouldReturn(['images[0][file]' => 'filePath']);
        $this->addFile('images[0][file]', 'filePath2');
        $this->getFiles()->shouldReturn(['images[0][file]' => 'filePath2']);
    }
}