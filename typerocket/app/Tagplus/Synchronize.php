<?php

namespace App\Tagplus;

/**
 * Interface Synchronize
 */
interface Synchronize
{
    public function synchronizeFrom($wpId);

    public function synchronizeAll();
}
