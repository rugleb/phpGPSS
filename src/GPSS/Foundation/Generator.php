<?php

namespace GPSS\Foundation;

use GPSS\Support\Concerns\HasModel;
use GPSS\Foundation\Transact\Transact;

/**
 * The Generator abstract base class.
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
        $name = $this->getTransactName();           // имя классаэкземпляра транзакта
        $number = $this->getTransactNumber();       // номер нового транзакта

        // после создания нового транзакта следует обновить время создания нового транзакта.
        // оно должно совпадать с временем прихода только что созданного транзакта.
        $this->setGenerateTime($time);

        // возвращаем инициализированный транзакт.
        return Transact::make($name)->setNumber($number)->setTime($time);
    }

    /**
     * Make new generator.
     *
     * @param string $generator
     * @return Generator
     */
    public static function make(string $generator): Generator
    {
        return new $generator;
    }

}