<?php

namespace GPSS\Foundation\Queue;

use GPSS\Support\Contracts\Stringable;
use GPSS\Foundation\Transact\Transact;
use GPSS\Foundation\Transact\TransactsCollection;

/**
 * The Queue base class.
 * This class is intended for processing and storing transactions in the queue.
 *
 * You can access a queue as a collection of transactions in it.
 * @mixin TransactsCollection
 *
 * Is part of the GPSS\Foundation package.
 * @package GPSS\Foundation\Queue
 */
class Queue implements Stringable
{
    /**
     * Queue statistic.
     *
     * @var QueueStatistic
     */
    protected $statistic;

    /**
     * Transacts in the queue.
     *
     * @var TransactsCollection
     */
    protected $transacts;

    /**
     * Queue constructor.
     */
    public function __construct()
    {
        $this->statistic = QueueStatistic::make($this);
        $this->transacts = TransactsCollection::make();
    }

    /**
     * Enter transact into the queue.
     *
     * @param Transact $transact
     * @return Queue
     */
    public function enter(Transact $transact): Queue
    {
        $this->transacts->push($transact);
        $this->statistic->enter($transact);

        return $this;
    }

    /**
     * Enter transact if it not in a queue.
     *
     * @param Transact $transact
     * @return Queue
     */
    public function enterIfHasNot(Transact $transact): Queue
    {
        if (! $this->has($transact)) {
            $this->enter($transact);
        }

        return $this;
    }

    /**
     * Output transact from the queue.
     *
     * @param Transact $transact
     * @return Queue
     */
    public function out(Transact $transact): Queue
    {
        $this->transacts->forget($transact);
        $this->statistic->out($transact);

        return $this;
    }

    /**
     * Get transacts list.
     *
     * @return TransactsCollection
     */
    public function getTransacts(): TransactsCollection
    {
        return $this->transacts;
    }

    /**
     * Get queue statistic.
     *
     * @return QueueStatistic
     */
    public function getStatistic(): QueueStatistic
    {
        return $this->statistic;
    }

    /**
     * Get the instance as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return "Transacts in queue:<br />{$this->transacts}<br />{$this->statistic}";
    }

    /**
     * Make new queue.
     *
     * @return Queue
     */
    public static function make(): Queue
    {
        return new static;
    }

    /**
     * Call as collection.
     *
     * @param callable $method    Method name
     * @param array $arguments    Arguments array
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return $this->transacts->$method(...$arguments);
    }

}
