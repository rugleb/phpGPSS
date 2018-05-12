<?php

namespace GPSS\Support\Contracts;

/**
 * The Stringable interface.
 *
 * @package GPSS\Support\Contracts
 */
interface Stringable
{

    /**
     * Get the instance as a string.
     *
     * @return string
     */
    public function __toString(): string;

}
