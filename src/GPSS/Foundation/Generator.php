<?php

namespace GPSS\Foundation;

use GPSS\Support\Concerns\HasModel;
use GPSS\Foundation\Transact\Transact;

/**
 * The Generator abstract base class.
 *
 * The generator determines the time and period for the application to appear in the model.
 * The object instance task is to create a new transact after a specified
 * time interval and enter it into the Model.
 *
 * Is part of the GPSS\Foundation package.
 * @package GPSS\Foundation
 */
abstract class Generator
{
    use HasModel;

    /**
     * Time to create a new transact.
     *
     * @var int
     */
    protected $generateTime;

    /**
     * Generator constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get time to create a new transact.
     *
     * @return int
     */
    public function getGenerateTime()
    {
        if (! $this->generateTime) {
            $this->setGenerateTime($this->getModel()->getTime());
        }

        return $this->generateTime;
    }

    /**
     * Set time to create a new transact.
     *
     * @param int $time
     * @return Generator
     */
    public function setGenerateTime(int $time): Generator
    {
        $this->generateTime = $time;

        return $this;
    }

    /**
     * Get delay time.
     *
     * @return int
     */
    abstract public function getDelayTime(): int;

    /**
     * Get transact class name.
     *
     * @return string
     */
    abstract public function getTransactName(): string;

    /**
     * Get transact number.
     *
     * @return int
     */
    protected function getTransactNumber(): int
    {
        return $this->getModel()->count();
    }

    /**
     * Get transact creation time.
     *
     * @return int
     */
    protected function getTransactTime(): int
    {
        return $this->getModel()->getTime() + $this->getDelayTime();
    }

    /**
     * Make new transact.
     *
     * @return Transact
     */
    public function makeTransact(): Transact
    {
        $time = $this->getTransactTime();           // время прихода нового транзакта
        $number = $this->getTransactNumber();       // номер нового транзакта
        $instance = $this->getTransactName();       // имя классаэкземпляра транзакта

        // после создания нового транзакта следует обновить время создания нового транзакта.
        // оно должно совпадать с временем прихода только что созданного транзакта.
        $this->setGenerateTime($time);

        /**
         * @var Transact $transact
         */
        $transact = new $instance;

        return $transact->setNumber($number)->setTime($time);
    }

    /**
     * Make new generator.
     *
     * @return Generator
     */
    public static function make(): Generator
    {
        return new static;
    }

}