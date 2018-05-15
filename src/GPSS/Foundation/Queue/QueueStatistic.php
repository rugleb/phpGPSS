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
     * Queue instance.
     *
     * @var Queue
     */
    protected $queue;

    /**
     * QueueStatistic constructor.
     *
     * @param Queue $queue
     */
    public function __construct(Queue &$queue)
    {
        $this->queue = $queue;

        $this->clear();
    }

    /**
     * Enter transact into the queue.
     *
     * @param Transact $transact
     * @return QueueStatistic
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
     */
    protected function updateQueueMaxLength(): QueueStatistic
    {
        $this->queueMaxLength = max($this->queue->count(), $this->queueMaxLength);

        return $this;
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
     */
    public function clear(): QueueStatistic
    {
        $this->queueMaxLength = 0;

        $this->outs = TransactsCollection::make();
        $this->enters = TransactsCollection::make();

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
     * Make new queue statistic.
     *
     * @param Queue $queue
     * @return QueueStatistic
     */
    public static function make(Queue &$queue): QueueStatistic
    {
        return new static ($queue);
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
