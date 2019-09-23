<?php

namespace App\Tagplus;

use App\Models\Config;

/**
 * Class Product
 *
 * @property integer from_id
 * @package  App\Models
 */
class ProductSynchronize implements Synchronize
{
    use SynchronizeActions;

    protected $optionLastSync = 'last_sync_product';

    protected function fetchItems($fromId, $useCache)
    {
        if ($fromId > 0) {
            $query = ['id' => $fromId];
        } elseif ($fromId === null) {
            $query = [
                'ativo' => 1,
                'comercializavel' => 1,
            ];
            $since = Config::getOption($this->optionLastSync);
            if ($since) {
                $query['since'] = $since;
            }
        } else {
            return null;
        }
        $items = (new Product(null))
            ->setUseCache($useCache)
            ->fetch($query);

        return $items;
    }

    public function synchronizeFrom($wpId, $useCache = true)
    {
        $fromItems = $this->fetchItems($wpId, $useCache);

        /* @var \App\Tagplus\Product $fromItem */
        foreach ($fromItems as $fromItem) {
            if (!$fromItem->getSku()) {
                continue;
            }

            $productWp = (new \App\Models\Product())
                ->findBySku($fromItem->getSku());

            if ($productWp->getID()) {
                $productWp->updateFrom($fromItem);
            } else {
                $productWp->createFrom($fromItem);
            }
        }

        if ($wpId && isset($productWp)) {
            return $productWp;
        }
        return null;
    }
}
