**phpGPSS** - a library written in PHP, is an analog of the GPSS modeling language. 
Using this library, you can easily simulate and analyze your system, find out how effective your processes are.

For example, create a traffic simulator.

First of all, we define the transact - **Car**:

```php
use GPSS\Foundation\Transact\Transact;

/**
 * The Car class.
 */
class Car extends Transact
{
    //
}
```

Next, define a **Generator** that will generate new **Transacts** (in our case **Car**) at a certain point in time:

```php

use GPSS\Foundation\Generator;

/**
 * The CarGenerator class.
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
```

Next, define the **Service** that will handle our **Car** transacts created by the **CarGenerator**.  
Here we added a **HasModel**, which will allow us to use the **Queue** to enter the device: **RoadService**.  
The most important punctuation is the definition of the **handle** method, which contains the logic of transaction processing by the service.  
Also, we set the transaction processing time in the device, for which the method **getDelayTime**.  
```php
use GPSS\Foundation\Service\Service;
use GPSS\Foundation\Service\HasQueue;
use GPSS\Foundation\Transact\Transact;

/**
 * The RoadService class.
 */
class RoadService extends Service
{
    use HasQueue;

    /**
     * Handle input transact.
     *
     * @param Transact $transact
     * @return mixed
     */
    public function handle(Transact &$transact)
    {
        if ($this->isFree()) {
            return $this->seize($transact);
        }

        if ($this->isProcessing($transact)) {

            if ($this->canRelease($transact)) {
                return $this->release()->terminate($transact);
            }

            return $this;
        }
        
        return $this->queue($transact);
    }

    /**
     * Get delay time.
     *
     * @return int
     */
    public function getDelayTime(): int
    {
        return rand(2, 8) * 10;
    }

}
```

Now, everything is ready. It remains to create an instance of the **Model** and run the simulation.  
Pay attention to the **config** that the **Model** accepts.  
This config connects services, transactions and generators.

```php

require_once __DIR__ . '/vendor/autoload.php';

use App\Car;
use App\RoadService;
use App\CarGenerator;
use GPSS\Foundation\Model;

$config = [
    'map' => [
        Car::class => RoadService::class,
    ],
    'services' => [
        RoadService::class,
    ],
    'generators' => [
        CarGenerator::class,
        CarGenerator::class,
    ],
];

$model = new Model($config);
$model->simulate(200);
```