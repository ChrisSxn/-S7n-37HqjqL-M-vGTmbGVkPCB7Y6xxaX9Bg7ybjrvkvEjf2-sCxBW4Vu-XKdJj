<?php

namespace Woub\City\Application\Actions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Woub\City\Application\Contracts\CityRepositoryInterface;

final readonly class UnlikeCity
{
    public function __construct(
        private int $cityId,
        private int $userId
    ) {
    }

    public function handle(CityRepositoryInterface $cityRepository): void
    {
        $city = $cityRepository->get($this->cityId);
        
        if ($city === null) {
            throw new ModelNotFoundException('City not found.');
        }

        $cityRepository->delete(
            userId: $this->userId,
            cityId: $this->cityId
        );
    }
}

