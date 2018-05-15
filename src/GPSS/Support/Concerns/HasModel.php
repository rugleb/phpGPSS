<?php

namespace GPSS\Support\Concerns;

use GPSS\Foundation\Model;

/**
 * The HasModel trait.
 *
 * @package src\GPSS\Support\Concerns
 */
trait HasModel
{
    /**
     * The Model instance.
     *
     * @var Model|null
     */
    protected $model = null;

    /**
     * Get Model instance.
     *
     * @return Model|null
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set Model instance.
     *
     * @param Model $model    Model instance.
     * @return static
     */
    public function setModel(Model &$model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Determines if the model instance is installed.
     *
     * @return bool
     */
    public function hasModel(): bool
    {
        return $this->getModel() instanceof Model;
    }

}
