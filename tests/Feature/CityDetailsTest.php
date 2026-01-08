<?php

namespace Tests\Feature;

use GuzzleHttp\Promise as P;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;
use Woub\City\Application\Contracts\GoogleMapsServiceInterface;
use Woub\City\Application\Contracts\WeatherServiceInterface;
use Woub\City\Domain\Image;
use Woub\City\Domain\Weather;
use Woub\Models\User;

final class CityDetailsTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(): void
    {
        $user = User::factory()->create();
        Auth::login($user);
    }

    private function mockServices(): void
    {
        $googleMock = Mockery::mock(GoogleMapsServiceInterface::class);
        $googleMock->shouldReceive('getImage')->andReturn(P\Create::promiseFor(new Image('https://example.com/image.jpg')));
        $googleMock->shouldReceive('getCoordinates')->andReturn(P\Create::promiseFor(['latitude' => 45.4642, 'longitude' => 9.19]));
        $this->app->instance(GoogleMapsServiceInterface::class, $googleMock);

        $weatherMock = Mockery::mock(WeatherServiceInterface::class);
        $weatherMock->shouldReceive('getWeather')->andReturn(P\Create::promiseFor(new Weather(
            temperature: 12.3,
            description: 'Clear sky',
            icon: 'https://example.com/icon.png',
            timezone: 'Europe/Rome',
            time: '2026-01-07T14:30:00',
            windspeed: 3.6,
            winddirection: 180,
            elevation: 122,
            weathercode: 0,
            is_day: 1
        )));
        $this->app->instance(WeatherServiceInterface::class, $weatherMock);
    }

    public function test_get_city_details_returns_success(): void
    {
        $this->authenticate();
        $this->mockServices();
        
        $response = $this->getJson('/api/cities/Milan, Italy/details');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'city' => 'Milan, Italy'
                ]
            ]);
    }
}
