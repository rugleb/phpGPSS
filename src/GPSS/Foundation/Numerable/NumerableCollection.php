<?php

namespace GPSS\Foundation\Numerable;

use GPSS\Support\Contracts\Stringable;
use Tightenco\Collect\Support\Collection as BaseCollection;

/**
 * The Numerable NumerableCollection base class.
 *
 * @package GPSS\Foundation\NumerableCollection
 */
class NumerableCollection extends BaseCollection implements Stringable
{
    /**
     * Determine if an item exists in the collection by key.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function has($key): bool
    {
        if ($key instanceof NumerableInterface) {
            return $this->hasNumerable($key);
        }

        return parent::has($key);
    }

    /**
     * Determine if a Numerable exists in the collection by key.
     *
     * @param NumerableInterface $numerable    Numerable instance
     * @return bool
     */
    public function hasNumerable(NumerableInterface $numerable): bool
    {
        return (bool) $this->first(function (NumerableInterface $item) use ($numerable) {
            return $item->getNumber() === $numerable->getNumber();
        });
    }

    /**
     * Remove an item from the collection by key.
     *
     * @param  string|array  $keys
     * @return NumerableCollection
     */
    public function forget($keys): NumerableCollection
    {
        if ($keys instanceof NumerableInterface) {
            return $this->forgetNumerable($keys);
        }

        return parent::forget($keys);
    }

    /**
     * Remove a Numerable instance from the collection by key.
     *
     * @param NumerableInterface $numerable
     * @return NumerableCollection
     */
    public function forgetNumerable(NumerableInterface $numerable): NumerableCollection
    {
        $key = $this->getKey($numerable);

        if (false !== $key) {
            $this->forget($key);
        }

        return $this;
    }

    /**
     * Get Numerable instance key of the collection or null.
     *
     * @param NumerableInterface $numerable
     * @return int|false
     */
    public function getKey(NumerableInterface $numerable)
    {
        $key = false;

        $this->each(function (NumerableInterface $item, int $index) use ($numerable, &$key) {

            if ($item->getNumber() === $numerable->getNumber()) {
                $key = $index;

                return false;
            }

        });

        return $key;
    }

    /**
     * Clear collection.
     *
     * @return NumerableCollection
     */
    public function clear(): NumerableCollection
    {
        $this->items = [];

        return $this;
    }

    /**
     * Get the instance as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->reduce(function ($string, Numerable $item) {
            return $string . $item . '<br />';
        }, '');
    }

}
