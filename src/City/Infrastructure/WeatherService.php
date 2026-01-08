<?php

namespace Woub\City\Infrastructure;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\PromiseInterface;
use Woub\City\Application\Contracts\WeatherServiceInterface;
use Woub\City\Domain\Weather;

final class WeatherService implements WeatherServiceInterface
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.open-meteo.com',
            'timeout' => 5,
        ]);
    }

    private function makeRequest(string $endpoint, array $query = []): ?array
    {
        try {
            $response = $this->client->get($endpoint, [
                'query' => $query,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return null;
        }
    }

    private function getWeatherDescription(int $weathercode): string
    {
        return match ($weathercode) {
            0 => 'Clear sky',
            1, 2, 3 => 'Mainly clear, partly cloudy, and overcast',
            45, 48 => 'Fog and depositing rime fog',
            51, 53, 55 => 'Drizzle: Light, moderate, and dense intensity',
            56, 57 => 'Freezing Drizzle: Light and dense intensity',
            61, 63, 65 => 'Rain: Slight, moderate and heavy intensity',
            66, 67 => 'Freezing Rain: Light and heavy intensity',
            71, 73, 75 => 'Snow fall: Slight, moderate, and heavy intensity',
            77 => 'Snow grains',
            80, 81, 82 => 'Rain showers: Slight, moderate, and violent',
            85, 86 => 'Snow showers: Slight and heavy',
            95 => 'Thunderstorm: Slight or moderate',
            96, 99 => 'Thunderstorm with slight and heavy hail',
            default => 'Unknown'
        };
    }

    private function getWeatherIcon(int $weathercode, int $isDay): string
    {
        $suffix = $isDay === 1 ? 'd' : 'n';
        
        $iconCode = match (true) {
            $weathercode === 0 => '01',
            $weathercode <= 3 => sprintf('0%d', $weathercode),
            in_array($weathercode, [45, 48]) => '50',
            in_array($weathercode, [51, 53, 55, 80, 81, 82]) => '09',
            in_array($weathercode, [61, 63, 65]) => '10',
            in_array($weathercode, [56, 57, 66, 67, 71, 73, 75, 77, 85, 86]) => '13',
            in_array($weathercode, [95, 96, 99]) => '11',
            default => '01',
        };

        return "https://openweathermap.org/img/wn/{$iconCode}{$suffix}@2x.png";
    }

    public function getWeather(float $latitude, float $longitude): PromiseInterface
    {
        $promise = $this->client->getAsync('/v1/forecast', [
            'query' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current_weather' => 'true',
                'timezone' => 'auto',
            ],
        ]);

        return $promise->then(function ($response) {
            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data) || !isset($data['current_weather']['temperature'])) {
                return null;
            }

            $currentWeather = $data['current_weather'];
            $weathercode = (int) ($currentWeather['weathercode'] ?? 0);
            $isDay = (int) ($currentWeather['is_day'] ?? 1);
            $temperature = (float) $currentWeather['temperature'];
            $windspeed = (float) ($currentWeather['windspeed'] ?? 0);
            $winddirection = (int) ($currentWeather['winddirection'] ?? 0);
            $time = (string) ($currentWeather['time'] ?? '');
            $timezone = (string) ($data['timezone'] ?? '');
            $elevation = (int) ($data['elevation'] ?? 0);

            return new Weather(
                temperature: $temperature,
                description: $this->getWeatherDescription($weathercode),
                icon: $this->getWeatherIcon($weathercode, $isDay),
                timezone: $timezone,
                time: $time,
                windspeed: $windspeed,
                winddirection: $winddirection,
                elevation: $elevation,
                weathercode: $weathercode,
                is_day: $isDay
            );
        })->otherwise(function () {
            return null;
        });
    }
}
