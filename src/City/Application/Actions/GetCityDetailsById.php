<?php

namespace Woub\City\Application\Actions;

use Carbon\Carbon;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise as P;
use Illuminate\Support\Facades\Cache;
use Woub\City\Application\Contracts\CityRepositoryInterface;
use Woub\City\Application\Contracts\GoogleMapsServiceInterface;
use Woub\City\Application\Contracts\WeatherServiceInterface;
use Woub\City\Domain\City;
use Woub\City\Domain\CityDetails;
use Woub\City\Domain\CityDetailsEntity;
use Woub\City\Domain\Image;
use Woub\City\Domain\Weather;

final readonly class GetCityDetailsById
{
    private const CACHE_TTL = 60;

    public function __construct(
        private int $cityId
    ) {
    }

    public function handle(
        CityRepositoryInterface $cityRepository,
        GoogleMapsServiceInterface $googleMapsService,
        WeatherServiceInterface $weatherService
    ): PromiseInterface
    {
        $city = $cityRepository->get($this->cityId);

        $cached = Cache::get($this->getCacheKey());
        if ($cached !== null) {
            return P\Create::promiseFor($cached);
        }

        return $this->fetchCityDetails($city, $googleMapsService, $weatherService);
    }

    private function fetchCityDetails(City $city, GoogleMapsServiceInterface $googleMapsService, WeatherServiceInterface $weatherService): PromiseInterface
    {
        $promises = [$googleMapsService->getImage(cityName: $city->name)];
        
        if ($city->latitude !== null && $city->longitude !== null) {
            $promises[] = $weatherService->getWeather(latitude: $city->latitude, longitude: $city->longitude);
        }

        return P\Utils::all($promises)->then(function (array $results) use ($city): CityDetailsEntity {
            [$image, $weather] = [$results[0], $results[1] ?? null];
            $entity = $this->createCityDetailsEntity($city, $image, $weather, $this->formatCacheAge(time()));

            if ($weather !== null) {
                Cache::put($this->getCacheKey(), $entity, self::CACHE_TTL);
            }

            return $entity;

        })->otherwise(function () use ($city): CityDetailsEntity {

            return $this->createCityDetailsEntity($city, null, null);

        });
    }

    private function createCityDetailsEntity(City $city, ?Image $image, ?Weather $weather, ?string $cacheAge = null): CityDetailsEntity
    {
        $cityDetails = new CityDetails(
            city: $city->name,
            image: $image,
            weather: $weather,
            latitude: $city->latitude,
            longitude: $city->longitude
        );

        return new CityDetailsEntity(
            cityDetails: $cityDetails,
            city_id: $city->id,
            cache_age: $cacheAge
        );
    }

    private function getCacheKey(): string
    {
        return 'city.details.' . $this->cityId;
    }

    private function formatCacheAge(int $timestamp): string
    {
        return Carbon::createFromTimestamp($timestamp)
            ->setTimezone('Europe/Amsterdam')
            ->format('d/m/Y H:i:s');
    }
}

