<?php

namespace Woub\City\Application\Contracts;

use Woub\City\Domain\City;
use Woub\City\Domain\CityCollection;

interface CityRepositoryInterface
{
    public function get(int $cityId): ?City;

    public function list(int $userId): CityCollection;

    public function persist(int $userId, string $cityName, ?float $latitude = null, ?float $longitude = null): City;

    public function delete(int $userId, int $cityId): void;
}


