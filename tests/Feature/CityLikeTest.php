<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Woub\City\Domain\CityModel;
use Woub\Models\User;

final class CityLikeTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(): User
    {
        $user = User::factory()->create();
        Auth::login($user);
        return $user;
    }

    public function test_like_city_creates_relationship(): void
    {
        $user = $this->authenticate();

        $this->postJson('/api/cities', ['city_name' => 'Paris, France']);

        $this->assertTrue($user->likedCities()->where('name', 'Paris, France')->exists());
    }

    public function test_unlike_city_removes_relationship(): void
    {
        $user = $this->authenticate();
        $city = CityModel::firstOrCreate(['name' => 'Berlin, Germany']);
        $user->likedCities()->attach($city->id);

        $this->deleteJson("/api/cities/{$city->id}");

        $this->assertFalse($user->likedCities()->where('city_id', $city->id)->exists());
    }

    public function test_like_city_requires_authentication(): void
    {
        $response = $this->postJson('/api/cities', ['city_name' => 'Vienna, Austria']);

        $response->assertStatus(401);
    }

    public function test_unlike_city_not_in_database_returns_404(): void
    {
        $this->authenticate();

        $response = $this->deleteJson('/api/cities/99999');

        $response->assertStatus(404);
    }
}
