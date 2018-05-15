<?php

namespace App;

use GPSS\Foundation\Generator;

/**
 * Class CarGenerator.
 *
 * @package App
 */
class CarGenerator extends Generator
{

    /**
     * Get delay time.
     *
     * @return int
     */
    public function getDelayTime(): int
    {
        return rand(5, 9) * 10;
    }

    /**
     * Get transact class name.
     *
     * @return string
     */
    public function getTransactName(): string
    {
        return Car::class;
    }

}
