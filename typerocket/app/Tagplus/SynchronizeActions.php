<?php

namespace App\Tagplus;

use App\Models\Config;

trait SynchronizeActions
{
    public function synchronizeAll()
    {
        $now = null;
        try {
            $now = (new \DateTime())->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
        }

        $this->synchronizeFrom(null);

        if ($now) {
            Config::setOption(
                $this->{'optionLastSync'},
                $now
            );
        }
    }
}
