<?php

namespace GPSS\Foundation\Transact;

use Tightenco\Collect\Support\Collection;
use GPSS\Foundation\Numerable\NumerableCollection;

/**
 * The Transacts Collection base class.
 *
 * Is part of the GPSS\Foundation package.
 * @package GPSS\Foundation\Transact
 */
class TransactsCollection extends NumerableCollection
{
    /**
     * Get transacts collection times of arrival.
     *
     * @return Collection
     */
    public function getTimesCollection()
    {
        return collect($this->map(function (Transact $transact) {
            return $transact->getTime();
        }));
    }

    /**
     * Get the instance as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->reduce(function ($string, Transact $transact) {
            return $string . $transact . '<br />';
        }, '');
    }

}
