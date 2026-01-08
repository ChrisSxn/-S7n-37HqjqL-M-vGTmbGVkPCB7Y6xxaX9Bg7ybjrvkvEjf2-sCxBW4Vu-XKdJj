<?php

namespace Woub\City\Application\Actions;

use GuzzleHttp\Promise\Utils as PromiseUtils;
use Woub\City\Application\Contracts\CityRepositoryInterface;
use Woub\City\Application\Contracts\GoogleMapsServiceInterface;
use Woub\City\Application\Contracts\WeatherServiceInterface;
use Woub\City\Domain\CityDetails;

final readonly class GetCityDetailsByName
{
    public function __construct(
        private string $cityName
    ) {
    }

    public function handle(
        CityRepositoryInterface $cityRepository,
        GoogleMapsServiceInterface $googleMapsService,
        WeatherServiceInterface $weatherService
    ): CityDetails {
        [$image, $coordinates] = PromiseUtils::all([
            $googleMapsService->getImage($this->cityName),
            $googleMapsService->getCoordinates($this->cityName)
        ])->wait();

        if ($coordinates !== null) {
            $weather = $weatherService->getWeather(
                latitude: $coordinates['latitude'], 
                longitude: $coordinates['longitude']
            )->wait();
        }

        return new CityDetails(
            city: $this->cityName,
            image: $image,
            weather: isset($weather) ? $weather : null,
            currency: null,
            latitude: $coordinates !== null ? ($coordinates['latitude'] ?? null) : null,
            longitude: $coordinates !== null ? ($coordinates['longitude'] ?? null) : null
        );
    }
}
