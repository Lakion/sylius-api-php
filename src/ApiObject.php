<?php

namespace Sylius\Api;

class ApiObject
{
    /**
     * @var int|string $id
     */
    private $id;
    /**
     * @var array $data
     */
    private $data;
    /**
     * @var array $files
     */
    private $files;
    /**
     * @var $uriParameters
     */
    private $uriParameters;

    /**
     * @param array $data
     * @param array $uriParameters
     * @param array $files
     */
    public function __construct(array $data = [], array $uriParameters = [], array $files = [])
    {
        $this->data = $data;
        $this->uriParameters = $uriParameters;
        $this->files = $files;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getUriParameters()
    {
        return $this->uriParameters;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function setDataValue($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function addFile($key, $filePath)
    {
        $this->files[$key] = $filePath;
    }
}
