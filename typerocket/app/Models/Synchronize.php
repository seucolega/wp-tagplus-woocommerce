<?php

namespace App\Models;

/**
 * Interface Synchronize
 */
interface Synchronize
{
    /**
     * Get Id from ERP
     *
     * @return null
     */
    public function getFromId();

    /**
     * Set Id from ERP
     *
     * @param integer $fromId Id
     *
     * @return $this
     */
    public function setFromId($fromId);

    /**
     * Update a WordPress object from ERP
     *
     * @param \App\Tagplus\Product $fromItem Product from TagPlus
     *
     * @return bool
     */
    public function updateFrom($fromItem);

    /**
     * Create a WordPress object from ERP
     *
     * @param \App\Tagplus\Product $fromItem Product from TagPlus
     *
     * @return bool
     */
    public function createFrom($fromItem);
}
