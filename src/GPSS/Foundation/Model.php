<?php

namespace GPSS\Foundation;

/**
 * The Model abstract base class.
 */
abstract class Model
{
    /**
     * Current simulation time.
     *
     * @var int
     */
    protected $time;

    /**
     * The Model configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * Model constructor.
     *
     * @param array $config    The model config.
     */
    public function __construct(array $config)
    {
        $this->time = null;

        $this->configure($config);
    }

    /**
     * Configure model.
     *
     * @param array $config
     * @return Model
     */
    protected function configure(array $config): Model
    {
        $this->config = $config;

        // TODO: run configuration

        return $this;
    }

    /**
     * Run the simulation.
     *
     * @param int $startTime    Initial simulation time
     * @param int $endTime      Final simulation time
     */
    public function simulate(int $startTime, int $endTime)
    {
        $this->time = $startTime;

        do {
            //
        } while ($this->time < $endTime);
    }

    /**
     * Set current simulation time.
     *
     * @param int $time    Current simulation time
     * @return Model       The Model instance
     */
    public function setTime(int $time): Model
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get current simulation time.
     *
     * @return int    Current simulation time
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Get Model configuration.
     *
     * @return array    Config
     */
    public function getConfig()
    {
        return $this->config;
    }

}
