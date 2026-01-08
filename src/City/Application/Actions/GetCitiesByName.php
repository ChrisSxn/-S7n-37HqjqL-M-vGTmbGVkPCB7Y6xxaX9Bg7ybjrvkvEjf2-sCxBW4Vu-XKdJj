<?php

namespace Woub\City\Application\Actions;

use Woub\City\Application\Contracts\GoogleMapsServiceInterface;
use Woub\City\Domain\CityCollection;

final readonly class GetCitiesByName
{
    public function __construct(
        private string $cityName
    ) {
    }

    public function handle(GoogleMapsServiceInterface $googleMapsService): CityCollection
    {
        return $googleMapsService->search($this->cityName);
    }
}

