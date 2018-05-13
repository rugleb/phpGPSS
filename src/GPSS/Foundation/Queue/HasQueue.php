<?php

namespace GPSS\Foundation\Queue;

/**
 * The HasQueue trait.
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

}
