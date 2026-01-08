<?php

namespace Woub\City\Application\Contracts;

use GuzzleHttp\Promise\PromiseInterface;
use Woub\City\Domain\Weather;

interface WeatherServiceInterface
{
    public function getWeather(float $latitude, float $longitude): PromiseInterface;
}

