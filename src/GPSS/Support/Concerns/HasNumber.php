<?php

namespace GPSS\Support\Concerns;

/**
 * The HasNumber trait.
 *
 * @package GPSS\Support\Concerns
 */
trait HasNumber
{
    /**
     * Instance number.
     *
     * @var int
     */
    protected $number;

    /**
     * Get instance number.
     *
     * @return int    Instance number
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set instance number.
     *
     * @param int $number    Instance number
     * @return static        Instance
     */
    public function setNumber(int $number)
    {
        $this->number = $number;

        return $this;
    }

}
