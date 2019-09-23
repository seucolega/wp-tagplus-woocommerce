<?php

namespace App\Tagplus;

class ProductCat extends Model
{
    // const PATH_TO_FETCH = 'categorias';
    protected $pathToFetch = 'categorias';
    protected $name = null;
    protected $location = null;
    protected $parentId = null;

    function __construct($response)
    {
        parent::__construct($response);

        $this->setName($response['descricao']);
        $this->setLocation($response['localizacao']);
        $this->setParentId(@$response['categoria_mae']['id']);
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return null
     */
    public function getSlug()
    {
        return sanitize_title($this->name);
    }

    /**
     * @param null $name Category name

     * @return void
     */
    protected function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location): void
    {
        $this->location = $location;
    }

    /**
     * @return integer
     */
    public function getParentId()
    {
        return (int)$this->parentId;
    }

    /**
     * @param integer $parentId
     */
    public function setParentId($parentId): void
    {
        $this->parentId = (int)$parentId;
    }

    public function fetchAll()
    {
        $query = [];
        return $this->fetch($query);
    }
}
