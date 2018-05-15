<?php

namespace GPSS\Foundation\Numerable;

use GPSS\Support\Contracts\Stringable;

/**
 * The Numerable abstract base class.
 *
 * Is part of the GPSS\Foundation package.
 * @package GPSS\Foundation\NumerableCollection
 */
abstract class Numerable implements NumerableInterface, Stringable
{
    /**
     * Instance number.
     *
     * @var int
     */
    protected $number = -1;

    /**
     * Get instance number.
     *
     * @return int    Instance number
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * Set instance number.
     *
     * @param int $number    Instance number
     * @return static
     */
    public function setNumber(int $number): Numerable
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get the instance as a string.
     *
     * @return string
     */
    abstract public function __toString(): string;

}
