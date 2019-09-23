<?php

namespace App\Tagplus;

class ProductCatSynchronize implements Synchronize
{
    use SynchronizeActions;

    protected $optionLastSync = 'last_sync_product_cat';

    protected function fetchItems($fromId)
    {
        if ($fromId > 0) {
            $query = ['id' => $fromId];
        } elseif ($fromId === null) {
            $query = [];
        } else {
            return null;
        }
        $items = (new ProductCat(null))
            ->fetch($query);

        return $items;
    }

    /**
     * @param int $wpId Category Id
     *
     * @return \App\Models\ProductCat|null
     */
    public function synchronizeFrom($wpId)
    {
        $fromItems = $this->fetchItems($wpId);

        /* @var \App\Tagplus\ProductCat $fromItem */
        foreach ($fromItems as $fromItem) {
            if (!$fromItem->getId()) {
                continue;
            }

            $categoryWp = (new \App\Models\ProductCat())
                ->findByFrom($fromItem);

            if ($categoryWp->getID()) {
                $categoryWp->updateFrom($fromItem);
            } else {
                $categoryWp->createFrom($fromItem);
            }
        }

        if ($wpId && isset($categoryWp)) {
            return $categoryWp;
        }
        return null;
    }
}
