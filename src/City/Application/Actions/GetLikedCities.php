<?php

namespace Woub\City\Application\Actions;

use Generator;
use GuzzleHttp\Promise\Utils;
use Woub\City\Application\Contracts\CityRepositoryInterface;
use Woub\City\Application\Contracts\GoogleMapsServiceInterface;
use Woub\City\Application\Contracts\WeatherServiceInterface;

final readonly class GetLikedCities
{
    private const MAX_CONCURRENT = 3;

    public function __construct(
        private int $userId
    ) {
    }

    public function handle(
        CityRepositoryInterface $cityRepository,
        GoogleMapsServiceInterface $googleMapsService,
        WeatherServiceInterface $weatherService
    ): Generator {
        $cityIds = array_column($cityRepository->list($this->userId)->toPlainArray(), 'id');
        $pending = $cityIds;
        $active = [];
        
        while (count($pending) > 0 || count($active) > 0) {
            while (count($active) < self::MAX_CONCURRENT && count($pending) > 0) {
                $cityId = array_shift($pending);
                $active[$cityId] = (new GetCityDetailsById($cityId))->handle($cityRepository, $googleMapsService, $weatherService);
            }
            
            yield from $this->yieldNextCompleted($active);
        }
    }

    private function yieldNextCompleted(array &$active): Generator
    {
        foreach ($active as $cityId => $promise) {
            try {
                yield $promise->wait();
            } catch (\Throwable $e) {
            }
            unset($active[$cityId]);
            return;
        }
    }
}
