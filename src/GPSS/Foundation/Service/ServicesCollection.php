<?php

namespace GPSS\Foundation\Service;

use GPSS\Foundation\Numerable\NumerableCollection;

/**
 * The Services Collection base class.
 *
 * Is part of the GPSS\Foundation package.
 * @package GPSS\Foundation\Service
 */
class ServicesCollection extends NumerableCollection
{

    /**
     * Get the instance as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->reduce(function ($string, Service $service) {
            return $string . $service . '<br />';
        }, '');
    }

}
