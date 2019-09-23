<?php

namespace App\Tagplus;

class Order extends Model
{
    protected $pathToFetch = 'pedidos';
    protected $id = null;
    protected $status = null;
    protected $clientId = null;
    protected $items = null;
    protected $invoices = null;
    protected $deliveryDate = null;
    protected $deliveryTime = null;
    protected $totalDelivery = null;
    protected $totalDiscount = null;
    protected $totalIncrease = null;
    protected $observation = null;

    function __construct($response = null)
    {
        parent::__construct($response);

        if (is_array($response)) {
            $this->setStatus($response['status']);
            $this->setClientId($response['cliente']);
            $this->setItems($response['itens']);
            $this->setInvoices($response['fatura']);
            $this->setDeliveryDate($response['data_entrega']);
            $this->setDeliveryTime($response['hora_entrega']);
            $this->setTotalDelivery($response['valor_frete']);
            $this->setTotalDiscount($response['valor_desconto']);
            $this->setTotalIncrease($response['valor_acrescimo']);
            $this->setObservation($response['observacoes']);
        }
    }

    // public function fetchAll()
    // {
    //     $query = [
    //     ];
    //     return $this->fetch($query);
    // }

    /**
     * @return null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param null $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return null
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param null $clientId
     *
     * @return $this
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param null $items
     *
     * @return $this
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @return null
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * @param null $invoices
     *
     * @return $this
     */
    public function setInvoices($invoices)
    {
        $this->invoices = $invoices;

        return $this;
    }

    /**
     * @return null
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    /**
     * @param null $deliveryDate
     *
     * @return $this
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    /**
     * @return null
     */
    public function getDeliveryTime()
    {
        return $this->deliveryTime;
    }

    /**
     * @param null $deliveryTime
     *
     * @return $this
     */
    public function setDeliveryTime($deliveryTime)
    {
        $this->deliveryTime = $deliveryTime;

        return $this;
    }

    /**
     * @return null
     */
    public function getTotalDelivery()
    {
        return $this->totalDelivery;
    }

    /**
     * @param null $totalDelivery
     *
     * @return $this
     */
    public function setTotalDelivery($totalDelivery)
    {
        $this->totalDelivery = $totalDelivery;

        return $this;
    }

    /**
     * @return null
     */
    public function getTotalDiscount()
    {
        return $this->totalDiscount;
    }

    /**
     * @param null $totalDiscount
     *
     * @return $this
     */
    public function setTotalDiscount($totalDiscount)
    {
        $this->totalDiscount = $totalDiscount;

        return $this;
    }

    /**
     * @return null
     */
    public function getTotalIncrease()
    {
        return $this->totalIncrease;
    }

    /**
     * @param null $totalIncrease
     *
     * @return $this
     */
    public function setTotalIncrease($totalIncrease)
    {
        $this->totalIncrease = $totalIncrease;

        return $this;
    }

    /**
     * @return null
     */
    public function getObservation()
    {
        return $this->observation;
    }

    /**
     * @param null $observation
     *
     * @return $this
     */
    public function setObservation($observation)
    {
        $this->observation = $observation;

        return $this;
    }

    protected function getItemsToRequest()
    {
        $itemsObj = $this->getItems();
        $itemsToRequest = [];
        $itemIndex = 1;
        /* @var \WC_Order_Item_Product $itemWc */
        foreach ($itemsObj as $itemWc) {
            $productWp = (new \App\Models\Product())
                ->findById($itemWc->get_product_id());
            if (!$productWp->getID()) {
                continue;
            }

            $itemsToRequest[] = [
                'item' => $itemIndex,
                'produto_servico' => $productWp->getFromId(),
                'qtd' => (float)$itemWc->get_quantity(),
                'valor_venda' => $productWp->getPriceId(),
            ];

            $itemIndex++;
        }

        return $itemsToRequest;
    }

    protected function getDataToRequest()
    {
        $data = [
            'status' => $this->getStatus(),
            // 'data_entrega' => $this->getDeliveryDate(),
            // 'hora_entrega' => $this->getDeliveryTime(),
            // 'departamento' => $this->,
            // 'vendedor' => ,
            'cliente' => $this->getClientId(),
            'itens' => $this->getItemsToRequest(),
            // 'faturas' => ,
            'valor_frete' => $this->getTotalDelivery(),
            'valor_desconto' => $this->getTotalDiscount(),
            'valor_acrescimo' => $this->getTotalIncrease(),
            // 'observacoes' => $this->,
        ];

        return $data;
    }

    public function create()
    {
        return (new Tagplus())
            ->post($this->pathToFetch, $this->getDataToRequest());
    }
}
