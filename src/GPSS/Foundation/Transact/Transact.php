<?php

namespace GPSS\Foundation\Transact;

use GPSS\Support\Concerns\HasNumber;
use GPSS\Support\Contracts\Stringable;
use Tightenco\Collect\Contracts\Support\Arrayable;

/**
 * The Transact abstract base class.
 *
 * @package GPSS\Foundation\Transact
 */
abstract class Transact implements Arrayable, Stringable
{
    use HasNumber;

    /**
     * Transact's time of arrival.
     *
     * @var int
     */
    protected $time;

    /**
     * Transact constructor.
     */
    public function __construct()
    {
        $this->time = null;
        $this->number = null;
    }

    /**
     * Get transact's time of arrival.
     *
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set transact's time of arrival.
     *
     * @param int $time    Time of arrival
     * @return Transact
     */
    public function setTime(int $time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            $this->number,
            $this->time
        ];
    }

    /**
     * Get the instance as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return "[{$this->number}, {$this->time}]";
    }

}
