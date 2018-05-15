<?php

namespace GPSS\Foundation\Transact;

use GPSS\Foundation\Numerable\Numerable;
use Tightenco\Collect\Contracts\Support\Arrayable;

/**
 * The Transact abstract base class.
 *
 * Is part of the GPSS\Foundation package.
 * @package GPSS\Foundation\Transact
 */
abstract class Transact extends Numerable implements Arrayable
{
    /**
     * Transact's time of arrival.
     *
     * @var int
     */
    protected $time = -1;

    /**
     * Service class name.
     *
     * @var string
     */
    protected $handler = '';

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
    public function getHandler(): string
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
        return is_string($this->handler) && class_exists($this->handler);
    }

    /**
     * Get transact's time of arrival.
     *
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * Set transact's time of arrival.
     *
     * @param int $time    Time of arrival
     * @return Transact
     */
    public function setTime(int $time): Transact
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            $this->getNumber(),
            $this->getTime()
        ];
    }

    /**
     * Make new transact.
     *
     * @return Transact
     */
    public static function make(): Transact
    {
        return new static;
    }

    /**
     * Get the instance as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return "[{$this->getNumber()}, {$this->getTime()}]";
    }

}
