<?php

namespace App\Tagplus;

use TypeRocket\Exceptions\ModelException;

class OrderSynchronize implements Synchronize
{
    use SynchronizeActions;

    public function synchronizeFrom($wpId)
    {
    }

    protected function fetchItems($wpId)
    {
        $items = [];

        if ($wpId > 0) {
            $order = (new \App\Models\Order())->findById($wpId);
            if ($order->getID()) {
                $items[] = $order;
            }
            // } elseif ($wpId === null) {
            // pegar todos
        }

        return $items;
    }

    public function synchronizeTo($wpId)
    {
        $ordersWp = $this->fetchItems($wpId);

        /* @var \App\Models\Order $orderWp */
        foreach ($ordersWp as $orderWp) {
            if ($orderWp->getFromId()) {
                $this->updateTo($orderWp);
            } else {
                $this->createTo($orderWp);
            }
        }
    }

    /**
     * @param \App\Models\Order $orderWp Order object
     *
     * @return \App\Models\Order
     */
    public function createTo($orderWp)
    {
        $customerErpId = (new CustomerSynchronize)
            ->synchronizeTo($orderWp->getCustomer())
            ->getId();

        $response = (new Order())
            ->setStatus('A')
            // ->setDeliveryDate(0)
            // ->setDeliveryTime(0)
            ->setClientId($customerErpId)
            ->setItems($orderWp->getItems())
            // ->setInvoices($orderWp->getInvoices())
            ->setTotalDelivery($orderWp->getTotalDelivery())
            ->setTotalDiscount($orderWp->getTotalDiscount())
            ->setTotalIncrease($orderWp->getTotalIncrease())
            ->setObservation($orderWp->getObservation())
            ->create();

        /* @var $orderErp Order */
        $orderErp = (new Order($response['response']));

        try {
            $orderWp
                ->setFromId($orderErp->getId())
                ->update();

            return $orderWp;
        } catch (ModelException $e) {
        }

        return null;
    }

    /**
     * @param \App\Models\Order $orderWp Order object
     *
     * @return \App\Models\Order
     */
    public function updateTo($orderWp)
    {
        return null;
    }
}
