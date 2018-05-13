<?php

namespace GPSS\Foundation;

use GPSS\Support\Concerns\HasModel;
use GPSS\Support\Concerns\HasNumber;
use GPSS\Support\Contracts\Stringable;
use GPSS\Foundation\Transact\Transact;

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
     * Service constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->model = null;
        $this->number = null;
        $this->transact = null;
    }

    /**
     * Handle input transact.
     *
     * @param Transact $transact
     * @return mixed
     */
    abstract public function handle(Transact &$transact);

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
     * Get delay time.
     *
     * @return int
     */
    public function getDelayTime(): int
    {
        return $this->delayTime;
    }

    /**
     * Set delay time.
     *
     * @param int $delayTime
     * @return Service
     */
    public function setDelayTime(int $delayTime): Service
    {
        $this->delayTime = $delayTime;

        return $this;
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
        // TODO: move Transact to futures

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
        // TODO: terminate transact from service

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
        return $this->transact instanceof Transact;
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
     * Get the instance as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return "Service #{$this->number}.<br />Current transact: {$this->transact}.<br />";
    }

}
