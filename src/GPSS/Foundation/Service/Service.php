<?php

namespace GPSS\Foundation\Service;

use GPSS\Support\Concerns\HasModel;
use GPSS\Support\Concerns\HasNumber;
use GPSS\Support\Contracts\Stringable;
use GPSS\Foundation\Transact\Transact;
use GPSS\Foundation\Transact\TransactsCollection;

/**
 * The Service abstract base class.
 *
 * Service is a service device, such as: cash register or elevator.
 * Work with this class the way you would do it in life.
 *
 * Is part of the GPSS\Foundation package.
 * @package GPSS\Foundation
 */
abstract class Service implements Stringable
{
    use HasModel,
        HasNumber;

    /**
     * Serviced transact.
     *
     * @var Transact
     */
    protected $transact;

    /**
     * Transact processing time.
     *
     * @var int
     */
    protected $delayTime = 0;

    /**
     * History of processed transacts.
     *
     * @var TransactsCollection
     */
    protected $story;

    /**
     * Service constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->number = null;
        $this->transact = null;

        $this->setFreshStory();
    }

    /**
     * Handle input transact.
     *
     * @param Transact $transact
     * @return mixed
     */
    abstract public function handle(Transact &$transact);

    /**
     * Get delay time.
     *
     * @return int
     */
    abstract public function getDelayTime(): int;

    /**
     * Get service story.
     *
     * @return TransactsCollection
     */
    public function getStory(): TransactsCollection
    {
        return $this->story;
    }

    /**
     * Set fresh service story.
     *
     * @return Service
     *
     * @throws \Exception
     */
    public function setFreshStory(): Service
    {
        $this->story = new TransactsCollection();

        return $this;
    }

    /**
     * Get processed transact.
     *
     * @return Transact|null
     */
    public function getTransact()
    {
        return $this->transact;
    }

    /**
     * Set processed transact.
     *
     * @param Transact $transact
     * @return Service
     */
    public function setTransact(Transact $transact): Service
    {
        $this->transact = $transact;

        return $this;
    }

    /**
     * Forget transact.
     *
     * @return Service
     */
    public function forgetTransact(): Service
    {
        $this->transact = null;

        return $this;
    }

    /**
     * Determines if the device is busy.
     *
     * @return bool
     */
    public function hasTransact(): bool
    {
        return $this->transact instanceof Transact;
    }

    /**
     * Sets when to output transact from service.
     *
     * @param Transact $transact
     * @return Service
     */
    public function setTimeOut(Transact &$transact): Service
    {
        // определяем время выхода транзакта из очереди как
        // сумма текущего модельного времения и ремени
        // обработки транзакта в текущем блоке.
        $timeOut = $this->getModel()->getTime() + $this->getDelayTime();

        // обновляем время выхода в транзакте
        $transact->setTime($timeOut);

        return $this;
    }

    /**
     * Delay transact in service.
     *
     * @param Transact $transact
     * @return Service
     */
    public function advance(Transact &$transact): Service
    {
        $this->setTimeOut($transact);
        $this->getModel()->moveToFutures($transact);

        return $this;
    }

    /**
     * Seize the service.
     *
     * @param Transact $transact
     * @return Service
     */
    public function seize(Transact &$transact): Service
    {
        return $this->setTransact($transact)->advance($transact);
    }

    /**
     * Release transact from service.
     *
     * @return Service
     */
    public function release(): Service
    {
        $this->getStory()->push($this->getTransact());
        $this->forgetTransact();

        return $this;
    }

    /**
     * Stop the transaction.
     *
     * @param Transact $transact
     * @return Service
     */
    public function terminate(Transact $transact): Service
    {
        $this->getModel()->getStorage()->remove($transact);

        return $this;
    }

    /**
     * Determines whether the device is occupied by input transact.
     *
     * @param Transact $transact
     * @return bool
     */
    public function isProcessing(Transact $transact): bool
    {
        return $this->hasTransact() && $this->getTransact()->getNumber() === $transact->getNumber();
    }

    /**
     * Determines whether it is possible to output transact from service.
     *
     * @param Transact $transact
     * @return bool
     */
    public function canRelease(Transact $transact): bool
    {
        return $this->getModel()->getTime() === $transact->getTime();
    }

    /**
     * Device if busy?
     *
     * @return bool
     */
    public function isBusy(): bool
    {
        return $this->getTransact() instanceof Transact;
    }

    /**
     * Device is free?
     *
     * @return bool
     */
    public function isFree(): bool
    {
        return ! $this->isBusy();
    }

    /**
     * Make new service.
     *
     * @param string $service    Service name
     * @return Service
     */
    public static function make(string $service): Service
    {
        return new $service;
    }

    /**
     * Get the instance as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return "<u>Service {$this->number}</u><br /><br />Current transact: {$this->transact}<br /><br />Story:<br />{$this->story}";
    }

}
