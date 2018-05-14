<?php

namespace GPSS\Foundation;

use GPSS\Foundation\Service\Service;
use GPSS\Support\Contracts\Stringable;
use GPSS\Foundation\Transact\Transact;
use Tightenco\Collect\Support\Collection;

/**
 * The Model abstract base class.
 *
 * @mixin Storage
 *
 * Is part of the GPSS\Foundation package.
 * @package GPSS\Foundation
 */
class Model implements Stringable
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
     * Transacts storage.
     *
     * @var Storage
     */
    protected $storage;

    /**
     * Services collection.
     *
     * @var Collection
     */
    protected $services;

    /**
     * Generators collection.
     *
     * @var Collection
     */
    protected $generators;

    /**
     * Model constructor.
     *
     * @param array $config    The model config.
     *
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        $this->time = null;

        $this->storage = new Storage;

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

        $this->makeServices($config['services']);
        $this->makeGenerators($config['generators']);

        return $this;
    }

    /**
     * Create services collection.
     *
     * @param string[] $services
     */
    protected function makeServices($services): void
    {
        $this->services = collect($services)->map(function (string $service, $index) {
            return Service::make($service)->setNumber($index)->setModel($this);
        });
    }

    /**
     * Create generators collection.
     *
     * @param string[] $generators
     */
    protected function makeGenerators($generators): void
    {
        $this->generators = collect($generators)->map(function (string $generator) {
            return Generator::make($generator)->setModel($this);
        });
    }

    /**
     * Run the simulation.
     *
     * @param int $endTime    Simulation time
     * @return Model
     */
    public function simulate(int $endTime): Model
    {
        $this->time = 0;

        do {

            $this->generateTransacts();             // пробуем сгенерировать новые транзакты
            $this->phaseCorrectTime();              // корректируем таймер
            $this->phaseView();                     // двигаем транзакты

        } while ($this->time < $endTime);

        return $this;
    }

    /**
     * The correct time phase.
     */
    protected function phaseCorrectTime()
    {
        // интерпретатор просматривает список БС и устанавливает таймер
        // в ближайший запланированный момент движения транзакта,
        // находящийся в начале списка БС.
        $this->time = $this->storage->getFutures()->getTimesCollection()->min();

        // затем перемещаем транзакты из списка БС с запланированным
        // временем движения в список ТС.
        $this->storage->getFutures()->each(function (Transact $transact) {
            if ($transact->getTime() === $this->getTime()) {
                $this->moveToCurrents($transact);
            }
        });

        // после того как для транзакта наступило запланированное время его движения,
        // и он оказался в списке ТС, то главная цель транзакта состоит как можно
        // раньше (КМР) продвинуться вперед, т.е. войти в следующий блок.
    }

    /**
     * The view phase.
     */
    protected function phaseView()
    {
        // просматриваем список текущих событий и для каждого
        // транзакта запускаем сервисную обработку.
        $this->getCurrents()->each(function (Transact $transact) {

            // каждый транзакт имеет свой текущий сервис-обработчик.
            // нам необходимо найти его объект и запустить обработку.
            $service = $this->findService($transact->getHandler());

            // в случае, если транзакт не был найден, то выкинем
            // предупреждение, чтобы не возникло коллизий.
            if (! $service instanceof Service) {
                throw new \Exception("Service {$transact->getHandler()} not found.");
            }

            // в случае успеха - запускаем обработку.
            $service->handle($transact);

        });
    }

    /**
     * Generate new transacts.
     */
    protected function generateTransacts(): void
    {
        $this->generators->each(function (Generator &$generator) {

            if ($this->getTime() === $generator->getGenerateTime()) {

                // создаем новый транзакт
                $transact = $generator->makeTransact();

                // далее устанавливаем ему обработчик и добавляем в хранилище
                $this->identifyTransactWithService($transact)->storage->add($transact);
            }

        });
    }

    /**
     * Identify transact with service by config.
     *
     * @param Transact $transact
     * @return Model
     */
    protected function identifyTransactWithService(Transact &$transact): Model
    {
        // ищем в конфиге по имени транзакту сервис-обработчик
        $handler = $this->config['map'][get_class($transact)];

        $transact->setHandler($handler);

        return $this;
    }

    /**
     * Find service by service number.
     *
     * @param string $serviceName    Service class name
     * @return Service|null
     */
    protected function findService(string $serviceName)
    {
        return $this->services->first(function (Service $service) use ($serviceName) {
            return $service instanceof $serviceName;
        });
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
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * Get Model configuration.
     *
     * @return array    Config
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get transacts storage.
     *
     * @return Storage
     */
    public function getStorage(): Storage
    {
        return $this->storage;
    }

    /**
     * Report model state.
     *
     * @param string $header
     */
    public function report(string $header): void
    {
        echo "<b>{$header}</b><br /><br />";
        echo $this;
    }

    /**
     * Call as collection.
     *
     * @param callable $method    Method name
     * @param array $arguments    Arguments array
     * @return mixed
     *
     * @throws \Exception
     */
    public function __call($method, $arguments)
    {
        return $this->storage->$method(...$arguments);
    }

    /**
     * Get the instance as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return "Time: {$this->time}<br /><br />{$this->storage}" . $this->services->reduce(function ($string, Service $service) {
            return $string . $service . '<br />';
            }, '');
    }

}
