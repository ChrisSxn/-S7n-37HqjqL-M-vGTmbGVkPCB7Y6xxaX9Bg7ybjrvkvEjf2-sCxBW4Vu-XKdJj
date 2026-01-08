<?php

namespace Woub\City\Application\Actions;

use Woub\City\Application\Contracts\CityRepositoryInterface;
use Woub\City\Application\Contracts\GoogleMapsServiceInterface;
use Woub\City\Domain\City;

final readonly class LikeCity
{
    public function __construct(
        private string $cityName,
        private int $userId
    ) {
    }

    public function handle(
        CityRepositoryInterface $cityRepository,
        GoogleMapsServiceInterface $googleMapsService
    ): City {
        $coordinates = $googleMapsService->getCoordinates($this->cityName)->wait();
        
        $latitude = null;
        $longitude = null;
        if ($coordinates !== null) {
            $latitude = $coordinates['latitude'];
            $longitude = $coordinates['longitude'];
        }

        return $cityRepository->persist($this->userId, $this->cityName, $latitude, $longitude);
    }
}

