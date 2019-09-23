<?php

namespace App\Tagplus;

class Model
{
    // const PATH_TO_FETCH = null;
    protected $pathToFetch;
    protected $id = null;
    protected $fields = [];
    protected $useCache = true;

    function __construct($response)
    {
        if (is_array($response)) {
            $this->fields = $response;

            $this->setId($response['id']);
        }
    }

    /**
     * Get fields
     *
     * @return array|null
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get a field
     *
     * @param string $field Field returned from API
     *
     * @return mixed|null
     */
    public function getField($field)
    {
        return isset($this->fields[$field])
            ? $this->fields[$field]
            : null;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id Category ID
     *
     * @return $this
     */
    protected function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function fetch($query)
    {
        $responseItems = Tagplus::fetch($this->pathToFetch, $query, $this->useCache);
        $items = [];
        foreach ($responseItems as $responseItem) {
            $class = get_class($this);
            $items[] = (new $class($responseItem));
        }
        return $items;
    }

    public function fetchFirst($itemId = null)
    {
        $query = [];
        if ($itemId > 0) {
            $query['id'] = $itemId;
        } elseif ($itemId === null) {
            $query['per_page'] = 1;
            $query['page'] = 1;
        } else {
            return null;
        }
        $responseItems = $this->fetch($query);
        if (isset($responseItems[0])) {
            return $responseItems[0];
        }
        return null;
    }

    /**
     * @param bool $useCache
     */
    public function setUseCache(bool $useCache)
    {
        $this->useCache = $useCache;
        return $this;
    }
}
