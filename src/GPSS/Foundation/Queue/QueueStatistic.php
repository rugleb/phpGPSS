<?php

namespace GPSS\Foundation\Queue;

use GPSS\Support\Contracts\Stringable;
use GPSS\Foundation\Transact\Transact;
use GPSS\Foundation\Transact\TransactsCollection;

/**
 * The QueueStatistic base class.
 * This class stores transaction statistics for the Queue instance.
 * 
 * Is part of the GPSS\Foundation package.
 * @package GPSS\Foundation\Queue
 */
class QueueStatistic implements Stringable
{
    /**
     * Queue max length.
     *
     * @var int
     */
    protected $queueMaxLength;

    /**
     * The list of enter transacts.
     *
     * @var TransactsCollection
     */
    protected $enters;

    /**
     * The list of out transacts.
     *
     * @var TransactsCollection
     */
    protected $outs;

    /**
     * QueueStatistic constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->clear();
    }

    /**
     * Enter transact into the queue.
     *
     * @param Transact $transact
     * @return QueueStatistic
     *
     * @throws \Exception
     */
    public function enter(Transact $transact): QueueStatistic
    {
        $this->enters->push($transact);
        $this->updateQueueMaxLength();

        return $this;
    }

    /**
     * Update queue max length.
     *
     * @return QueueStatistic
     *
     * @throws \Exception
     */
    protected function updateQueueMaxLength(): QueueStatistic
    {
        $this->queueMaxLength = max($this->transactsInQueue()->count(), $this->queueMaxLength);

        return $this;
    }

    /**
     * Get transacts list in the queue.
     *
     * @return TransactsCollection
     * 
     * @throws \Exception
     */
    public function transactsInQueue(): TransactsCollection
    {
        return new TransactsCollection($this->enters->filter(function (Transact $transact) {
            return $this->outs->has($transact);
        }));
    }

    /**
     * Output transact from the queue.
     *
     * @param Transact $transact
     * @return QueueStatistic
     */
    public function out(Transact $transact): QueueStatistic
    {
        $this->outs->push($transact);

        return $this;
    }

    /**
     * Clear statistic information.
     *
     * @return QueueStatistic
     *
     * @throws \Exception
     */
    public function clear(): QueueStatistic
    {
        $this->queueMaxLength = 0;

        $this->outs = new TransactsCollection();
        $this->enters = new TransactsCollection();

        return $this;
    }

    /**
     * Get enters transact list.
     *
     * @return TransactsCollection
     */
    public function getEnters(): TransactsCollection
    {
        return $this->enters;
    }

    /**
     * Get outs transact list.
     *
     * @return TransactsCollection
     */
    public function getOuts(): TransactsCollection
    {
        return $this->outs;
    }

    /**
     * Get the instance as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return "Max queue length: {$this->queueMaxLength}<br /><br />Enters:<br />{$this->enters}<br />Outs:<br />{$this->outs}";
    }

}
