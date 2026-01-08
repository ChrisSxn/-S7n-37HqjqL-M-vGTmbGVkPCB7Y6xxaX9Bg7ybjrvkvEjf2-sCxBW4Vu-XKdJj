<?php

namespace Woub\City\Infrastructure;

use Woub\City\Application\Contracts\CityRepositoryInterface;
use Woub\City\Domain\City;
use Woub\City\Domain\CityCollection;
use Woub\City\Domain\CityModel;
use Woub\Models\User;

final class CityRepository implements CityRepositoryInterface
{
    public function get(int $cityId): ?City
    {
        $city = CityModel::query()->find($cityId);
        return $city ? $this->toCity($city) : null;
    }

    public function persist(int $userId, string $cityName, ?float $latitude = null, ?float $longitude = null): City
    {
        $city = CityModel::query()->firstOrCreate(['name' => $cityName]);

        if ($latitude !== null && $longitude !== null && ($city->latitude === null || $city->longitude === null)) {
            $city->update(['latitude' => $latitude, 'longitude' => $longitude]);
            $city->refresh();
        }

        User::query()->findOrFail($userId)->likedCities()->syncWithoutDetaching([$city->id]);

        return $this->toCity($city);
    }

    public function list(int $userId): CityCollection
    {
        $user = User::query()->find($userId);
        if (!$user) {
            return CityCollection::fromArray([]);
        }

        $cities = CityCollection::fromArray($user->likedCities()
            ->get(['cities.id', 'cities.name', 'cities.latitude', 'cities.longitude'])
            ->map(fn (CityModel $model) => $this->toCity($model))
            ->all());

        return $cities;
    }

    public function delete(int $userId, int $cityId): void
    {
        User::query()->findOrFail($userId)->likedCities()->detach($cityId);
    }

    private function toCity(CityModel $model): City
    {
        return new City($model->name, $model->id, $model->latitude, $model->longitude);
    }
}
