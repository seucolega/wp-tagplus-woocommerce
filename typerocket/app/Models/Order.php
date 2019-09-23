<?php

namespace App\Models;

use \TypeRocket\Models\WPPost;

/**
 * Class Order
 *
 * @property integer from_id
 * @package  App\Models
 */
class Order extends WPPost implements Synchronize
{
    protected $postType = 'shop_order';
    protected $fromId = null;
    protected $totalDelivery = null;
    protected $totalDiscount = null;
    protected $totalIncrease = null;
    protected $observation = null;
    protected $items = null;
    // protected $invoices = null;

    /**
     * Update a WordPress object from ERP
     *
     * @param \App\Tagplus\Product $fromItem Product from TagPlus
     *
     * @return bool
     */
    public function updateFrom($fromItem)
    {
    }

    /**
     * Create a WordPress object from ERP
     *
     * @param \App\Tagplus\Product $fromItem Product from TagPlus
     *
     * @return bool
     */
    public function createFrom($fromItem)
    {
    }

    /**
     * Get Id from ERP
     *
     * @return null
     */
    public function getFromId()
    {
        if (!$this->fromId) {
            $this->fromId = (string)$this->getFieldValue('_from_id');
        }

        return $this->fromId;
    }

    /**
     * Set Id from ERP
     *
     * @param integer $fromId Id
     *
     * @return $this
     */
    public function setFromId($fromId)
    {
        if (!$this->getFromId()) {
            $this->fromId = $fromId;
            $this->setProperty('_from_id', $fromId);
        }

        return $this;
    }

    /**
     * @return null
     */
    public function getTotalDelivery()
    {
        if (!$this->totalDelivery) {
            // $this->totalDelivery = $this->getFieldValue('_order_shipping');
            $this->totalDelivery = (float)(new \WC_Order($this->getID()))
                ->get_shipping_total();
        }

        return $this->totalDelivery;
    }

    /**
     * @param null $totalDelivery
     *
     * @return Order
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
        if (!$this->totalDiscount) {
            // $this->totalDiscount = $this->getFieldValue('_cart_discount');
            $this->totalDiscount = (float)(new \WC_Order($this->getID()))
                ->get_total_discount();
        }

        return $this->totalDiscount;
    }

    /**
     * @param null $totalDiscount
     *
     * @return Order
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
        if (!$this->totalIncrease) {
            $fees = (new \WC_Order($this->getID()))->get_items('fee');
            $total = 0;
            /* @var $fee \WC_Order_Item_Fee */
            foreach ($fees as $fee) {
                $total += (float)$fee->get_total();
            }

            $this->totalIncrease = $total;
        }

        return $this->totalIncrease;
    }

    /**
     * @param null $totalIncrease
     *
     * @return Order
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
        // if (!$this->observation) {
        //     $this->observation = $this->getFieldValue('');
        // }

        // return $this->observation;
        return '';
    }

    /**
     * @param null $observation
     *
     * @return Order
     */
    public function setObservation($observation)
    {
        $this->observation = $observation;
        return $this;
    }

    /**
     * @return \WC_Order_Item[]
     */
    public function getItems()
    {
        $orderWc = new \WC_Order($this->getID());

        return $orderWc->get_items();
    }

    // /**
    //  * @param null $items
    //  * @return Order
    //  */
    // public function setItems($items)
    // {
    //     $this->items = $items;
    //     return $this;
    // }

    // /**
    //  * @return null
    //  */
    // public function getInvoices()
    // {
    //     return $this->invoices;
    // }

    // /**
    //  * @param null $invoices
    //  * @return Order
    //  */
    // public function setInvoices($invoices)
    // {
    //     $this->invoices = $invoices;
    //     return $this;
    // }

    public function getCustomer()
    {
        $orderWc = (new \WC_Order($this->getID()));
        $userWp = (new User())->findById($orderWc->get_customer_id());

        return $userWp->getID();
    }
}
