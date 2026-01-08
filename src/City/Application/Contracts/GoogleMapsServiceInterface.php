<?php

namespace Woub\City\Application\Contracts;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Woub\City\Domain\CityCollection;
use Woub\City\Domain\Image;

interface GoogleMapsServiceInterface
{
    public function search(string $cityName): CityCollection;

    public function getImage(string $cityName): PromiseInterface;

    public function getCoordinates(string $cityName): PromiseInterface;

    public function fetchPhoto(string $photoName): ?ResponseInterface;
}

