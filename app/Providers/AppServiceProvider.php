<?php

namespace Woub\Providers;

use Illuminate\Support\ServiceProvider;
use Woub\City\Application\Contracts\CityRepositoryInterface;
use Woub\City\Application\Contracts\GoogleMapsServiceInterface;
use Woub\City\Application\Contracts\WeatherServiceInterface;
use Woub\City\Infrastructure\CityRepository;
use Woub\City\Infrastructure\GoogleMapsService;
use Woub\City\Infrastructure\WeatherService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CityRepositoryInterface::class, CityRepository::class);
        $this->app->bind(GoogleMapsServiceInterface::class, GoogleMapsService::class);
        $this->app->bind(WeatherServiceInterface::class, WeatherService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

