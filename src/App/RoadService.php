<?php

namespace App;

use GPSS\Foundation\Service\Service;
use GPSS\Foundation\Service\HasQueue;
use GPSS\Foundation\Transact\Transact;

/**
 * The RoadService class.
 */
class RoadService extends Service
{
    use HasQueue;

    /**
     * Handle input transact.
     *
     * @param Transact $transact
     * @return mixed
     */
    public function handle(Transact &$transact)
    {
        // если устройство свободно, занимаем его новым транзактом
        if ($this->isFree()) {
            return $this->seize($transact);
        }

        // устройство занято именно этим транзактом
        if ($this->isProcessing($transact)) {

            // можем освободить устройство
            if ($this->canRelease($transact)) {
                // освобождаем устройство и удаляем транзакт
                return $this->release()->terminate($transact);
            }

            return $this;
        }

        // устройство занято другим транзактом, тогда
        // ставим пришедший транзакт в очередь.
        return $this->queue($transact);
    }

    /**
     * Get delay time.
     *
     * @return int
     */
    public function getDelayTime(): int
    {
        return rand(2, 8) * 10;
    }

}