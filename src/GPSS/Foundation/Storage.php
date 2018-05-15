<?php

namespace GPSS\Foundation;

use GPSS\Support\Contracts\Stringable;
use GPSS\Foundation\Transact\Transact;
use GPSS\Foundation\Transact\TransactsCollection;

/**
 * The Storage base class.
 *
 * The transaction storage is designed to work with a list of current and
 * future events, as well as to store the history of transactions.
 *
 * This class is a wrapper over a common list of transactions,
 * so you can work with it as a TransactsCollection.
 * @mixin TransactsCollection
 *
 * Is part of the GPSS\Foundation package.
 * @package GPSS\Foundation
 */
class Storage implements Stringable
{
    /**
     * Future events list.
     *
     * @var TransactsCollection
     */
    protected $futureEventsList;

    /**
     * Current event list.
     *
     * @var TransactsCollection
     */
    protected $currentEventsList;

    /**
     * Transacts story.
     *
     * @var TransactsCollection
     */
    protected $story;

    /**
     * TransactsStorage constructor.
     */
    public function __construct()
    {
        $this->story = TransactsCollection::make();
        $this->futureEventsList = TransactsCollection::make();
        $this->currentEventsList = TransactsCollection::make();
    }

    /**
     * Add transact in storage.
     *
     * @param Transact $transact
     * @return Storage
     */
    public function add(Transact &$transact): Storage
    {
        $this->futureEventsList->push($transact);

        return $this;
    }

    /**
     * Remove transact from storage.
     *
     * @param Transact $transact
     * @return Storage
     */
    public function remove(Transact &$transact): Storage
    {
        $this->futureEventsList->forget($transact);
        $this->currentEventsList->forget($transact);

        $this->story->push($transact);

        return $this;
    }

    /**
     * Move transact from the Future events list to the Current events list.
     *
     * @param Transact $transact
     * @return Storage
     */
    public function moveToCurrents(Transact &$transact): Storage
    {
        $this->futureEventsList->forget($transact);
        $this->currentEventsList->push($transact);

        return $this;
    }

    /**
     * Move transact from the Current events list to the Future events list.
     *
     * @param Transact $transact
     * @return Storage
     */
    public function moveToFutures(Transact &$transact): Storage
    {
        $this->currentEventsList->forget($transact);
        $this->futureEventsList->push($transact);

        return $this;
    }

    /**
     * Get future events list.
     *
     * @return TransactsCollection
     */
    public function getFutures(): TransactsCollection
    {
        return $this->futureEventsList;
    }

    /**
     * Get current events list.
     *
     * @return TransactsCollection
     */
    public function getCurrents(): TransactsCollection
    {
        return $this->currentEventsList;
    }

    /**
     * Get story.
     *
     * @return TransactsCollection
     */
    public function getStory(): TransactsCollection
    {
        return $this->story;
    }

    /**
     * Get all transacts collection.
     *
     * @return TransactsCollection
     */
    public function all(): TransactsCollection
    {
        return $this->futureEventsList->concat($this->currentEventsList);
    }

    /**
     * Make new storage.
     *
     * @return Storage
     */
    public static function make(): Storage
    {
        return new static;
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
        return $this->all()->$method(...$arguments);
    }

    /**
     * Get the instance as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return "Current events list:<br />{$this->currentEventsList}<br/>Future events list:<br />{$this->futureEventsList}<br />";
    }
    
}
