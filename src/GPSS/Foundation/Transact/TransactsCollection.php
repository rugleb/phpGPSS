<?php

namespace GPSS\Foundation\Transact;

use GPSS\Support\Contracts\Stringable;
use Tightenco\Collect\Support\Collection;

/**
 * The TransactsCollection base class.
 * This class should be used to store and work with Transact instances.
 *
 * This class is a wrapper over the base class of collections,
 * but many methods are redefined for ease of use.
 * @mixin Collection
 *
 * Is part of the GPSS\Foundation package.
 * @package GPSS\Foundation\Transact
 */
class TransactsCollection implements Stringable
{
    /**
     * Transacts collection.
     *
     * @var Collection
     */
    protected $collection;

    /**
     * TransactsCollection constructor.
     *
     * @param Transact[]|Collection|Transact $transacts    Transacts collection
     *
     * @throws \Exception
     */
    public function __construct($transacts = [])
    {
        $this->setCollection($transacts);
    }

    /**
     * Set new transacts collection.
     *
     * @param Transact[]|Collection|Transact $transacts    Transacts collection
     * @return TransactsCollection
     *
     * @throws \Exception
     */
    public function setCollection($transacts): TransactsCollection
    {
        $collection = $this->makeCollection($transacts);

        if (! $this->isValidCollection($collection)) {
            throw new \Exception('TransactCollection can only contains Transact instances.');
        }

        $this->collection = $collection;

        return $this;
    }

    /**
     * Checks whether the collection contains Transact instances.
     *
     * @param Collection $collection
     * @return bool
     */
    protected function isValidCollection(Collection $collection): bool
    {
        return $collection->every(function ($item) {
            return $item instanceof Transact;
        });
    }

    /**
     * Make collection from items.
     *
     * @param Transact[]|Collection|Transact $items    Transacts collection
     * @return Collection
     */
    protected function makeCollection($items): Collection
    {
        if ($items instanceof Collection) {
            return $items;
        } elseif (is_array($items)) {
            return collect($items);
        } else {
            return collect([$items]);
        }
    }

    /**
     * Push transact onto the end of the collection.
     *
     * @param Transact $transact
     * @return TransactsCollection
     */
    public function push(Transact $transact): TransactsCollection
    {
        $this->collection->push($transact);

        return $this;
    }

    /**
     * Checks whether a transact is stored in a collection.
     *
     * @param Transact $transact
     * @return bool
     */
    public function has(Transact $transact): bool
    {
        return (bool) $this->collection->first(function (Transact $item) use ($transact) {
            return $transact->getNumber() === $item->getNumber();
        });
    }

    /**
     * Remove transact from collection.
     *
     * @param Transact $transact
     * @return TransactsCollection
     */
    public function forget(Transact $transact): TransactsCollection
    {
        $key = $this->getKey($transact);

        if (null !== $key) {
            $this->collection->forget($key);
        }

        return $this;
    }

    /**
     * Return transact key of the collection or null.
     *
     * @param Transact $transact
     * @return int|null
     */
    public function getKey(Transact $transact)
    {
        $key = null;

        $this->collection->each(function (Transact $item, int $index) use ($transact, &$key) {

            if ($item->getNumber() === $transact->getNumber()) {
                $key = $index;

                return false;
            }

            return true;

        });

        return $key;
    }

    /**
     * Get collection of transact's time of arrival.
     *
     * @return Collection
     */
    public function getTimesCollection(): Collection
    {
        return $this->collection->map(function (Transact $transact) {
            return $transact->getTime();
        });
    }

    /**
     * Clear transacts collection.
     *
     * @return TransactsCollection
     */
    public function clear(): TransactsCollection
    {
        $this->collection = collect();

        return $this;
    }

    /**
     * Push all of the given items onto the collection.
     *
     * @param TransactsCollection $transacts
     * @return TransactsCollection
     *
     * @throws \Exception
     */
    public function concat(TransactsCollection $transacts): TransactsCollection
    {
        $collection = $this->collection->concat($transacts->all());

        return new static ($collection);
    }

    /**
     * Get the instance as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->collection->reduce(function ($string, Transact $transact) {
            return $string . $transact . '<br />';
        }, '');
    }

    /**
     * Call as collection.
     *
     * @param callable $method    Method name
     * @param array $arguments    Arguments array
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return $this->collection->$method(...$arguments);
    }

}
