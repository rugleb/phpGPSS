<?php

namespace GPSS\Foundation\Service;

use GPSS\Foundation\Queue\Queue;
use GPSS\Foundation\Transact\Transact;

/**
 * The HasQueue trait.
 *
 * This trait extends Services that need to move incoming transactions to the queue.
 * @mixin Service
 *
 * Is part of the GPSS\Foundation package.
 * @package GPSS\Foundation\Queue
 */
trait HasQueue
{
    /**
     * The queue instance.
     *
     * @var Queue
     */
    protected $queue;

    /**
     * HasQueue constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();

        $this->setFreshQueue();
    }

    /**
     * Get queue instance.
     *
     * @return Queue
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * Set queue instance.
     *
     * @param Queue $queue
     * @return static
     */
    public function setQueue(Queue &$queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * Set new queue instance.
     *
     * @return static
     *
     * @throws \Exception
     */
    public function setFreshQueue()
    {
        $this->queue = new Queue();

        return $this;
    }

    /**
     * Enter transact to queue.
     *
     * @param Transact $transact
     * @return Service
     *
     * @throws \Exception
     */
    public function queue(Transact $transact): Service
    {
        $this->getQueue()->enterIfHasNot($transact);

        return $this;
    }

    /**
     * Out from queue.
     *
     * @param Transact $transact
     * @return Service
     */
    public function depart(Transact $transact): Service
    {
        $this->getQueue()->out($transact);

        return $this;
    }

    /**
     * Enter first transact from queue to device if queue not empty.
     *
     * @return bool
     */
    protected function tryEnterTransactFromQueue(): bool
    {
        // если в очереди нет ожадающих места транзактов,
        // то операция прошла безуспешно.
        if ($this->getQueue()->isEmpty()) {
            return false;
        }

        // поскольку в очереди есть транзакты ожидающие места,
        // то согласно принципу FIFO приоритет встраивается
        // в порядке прихода транзакта в очередь.
        $transact = $this->getQueue()->first();

        // соответственно выводим этот транзакт из очереди
        // и занимаем им устройство текущее устройство.
        $this->depart($transact)->seize($transact);

        return true;
    }

    /**
     * Release the device.
     *
     * @return Service
     */
    public function release(): Service
    {
        parent::release();

        // поскольку девайс теперь свободен, то мы можем
        // занять его транзактом, который ожидает его
        // в очереди и в то же время, в ЦТС.
        $this->tryEnterTransactFromQueue();

        return $this;
    }

    /**
     * Get the instance as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return parent::__toString() . "<br />{$this->queue}";
    }

}
