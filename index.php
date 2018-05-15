<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Car;
use App\RoadService;
use App\CarGenerator;
use GPSS\Foundation\Model;

$config = [
    'map' => [
        Car::class => RoadService::class
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
