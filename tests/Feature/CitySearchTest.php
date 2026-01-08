<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;
use Woub\City\Application\Contracts\GoogleMapsServiceInterface;
use Woub\City\Domain\City;
use Woub\City\Domain\CityCollection;
use Woub\Models\User;

final class CitySearchTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(): void
    {
        $user = User::factory()->create();
        Auth::login($user);
    }

    private function mockGoogleMapsService(array $cityNames): void
    {
        $cities = array_map(fn($name) => new City($name), $cityNames);
        $collection = CityCollection::fromArray($cities);
        
        $mock = Mockery::mock(GoogleMapsServiceInterface::class);
        $mock->shouldReceive('search')->andReturn($collection);
        $this->app->instance(GoogleMapsServiceInterface::class, $mock);
    }

    public function test_search_returns_cities(): void
    {
        $this->authenticate();
        $this->mockGoogleMapsService(['Milan, Italy', 'Paris, France']);
        
        $response = $this->getJson('/api/cities/Milan/search');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    ['name' => 'Milan, Italy'],
                    ['name' => 'Paris, France']
                ]
            ]);
    }

    public function test_search_returns_empty_when_no_match(): void
    {
        $this->authenticate();
        $this->mockGoogleMapsService([]);
        
        $response = $this->getJson('/api/cities/InvalidCity/search');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => []
            ]);
    }
}
