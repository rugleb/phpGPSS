<?php

namespace GPSS\Foundation\Transact;

use GPSS\Support\Concerns\HasNumber;
use GPSS\Support\Contracts\Stringable;
use Tightenco\Collect\Contracts\Support\Arrayable;

/**
 * The Transact abstract base class.
 *
 * Is part of the GPSS\Foundation package.
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
     * Service class name.
     *
     * @var string
     */
    protected $handler;

    /**
     * Transact constructor.
     */
    public function __construct()
    {
        $this->time = null;
        $this->number = null;
        $this->handler = null;
    }

    /**
     * Set service class name.
     *
     * @param string $serviceName    Service name
     * @return Transact
     */
    public function setHandler(string $serviceName): Transact
    {
        $this->handler = $serviceName;

        return $this;
    }

    /**
     * Get service class name.
     *
     * @return string    Service name.
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Determines whether the handler is installed.
     *
     * @return bool
     */
    public function hasHandler(): bool
    {
        return is_string($this->handler);
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
     * Make new transact.
     *
     * @param string $transactName    Transact name
     * @return Transact
     */
    public static function make(string $transactName): Transact
    {
        return new $transactName;
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
