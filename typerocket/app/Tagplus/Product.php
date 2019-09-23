<?php

namespace App\Tagplus;

class Product extends Model
{
    protected $pathToFetch = 'produtos';
    protected $sku = null;
    protected $name = null;
    protected $priceId = null;
    protected $price = null;
    protected $categories = null;
    protected $isActive = null;
    protected $isMarketable = null;

    function __construct($response)
    {
        parent::__construct($response);

        $this->setSku($response['codigo']);
        $this->setName($response['descricao']);
        // $this->setPrice($response['valor_venda_varejo']);
        $this->setSalePrices($response['valores_venda']);
        $this->isActive = $response['ativo'] === true;
        $this->isMarketable = $this->isActive()
            && $response['comercializavel'] === true;

        // if ($this->getField('ativo') === false
        //     || $this->getField('comercializavel') === false
        // ) {
        //     return false;
        // }
    }

    /**
     * @return string Sku code
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param string $sku Product Sku code
     *
     * @return $this
     */
    protected function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null $name Product name

     * @return $this
     */
    protected function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return (float)$this->price;
    }

    /**
     * @param null $price Product price
     *
     * @return void
     */
    protected function setPrice($priceId, $price): void
    {
        $this->priceId = (int)$priceId;
        $this->price = (float)$price;
    }

    protected function setSalePrices($prices)
    {
        if (is_array($prices)) {
            foreach ($prices as $price) {
                if (@!$price['tipo_valor_venda']['padrao']) {
                    continue;
                }
                $this->setPrice($price['id'], $price['valor_venda']);
            }
        }

        return $this;
    }

    public function fetchAll()
    {
        $query = [
            'ativo' => 1,
            'comercializavel' => 1,
        ];
        return $this->fetch($query);
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @return boolean
     */
    public function isMarketable()
    {
        return $this->isMarketable;
    }

    /**
     * @return int
     */
    public function getPriceId()
    {
        return (int)$this->priceId;
    }
}
