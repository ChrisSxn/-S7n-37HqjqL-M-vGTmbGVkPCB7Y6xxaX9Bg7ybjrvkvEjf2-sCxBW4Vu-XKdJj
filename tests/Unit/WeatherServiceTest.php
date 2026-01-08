<?php

namespace Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise as P;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Psr\Http\Message\StreamInterface;
use Tests\TestCase;
use Woub\City\Domain\Weather;
use Woub\City\Infrastructure\WeatherService;

final class WeatherServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function getFixture(): array
    {
        $content = file_get_contents(__DIR__ . '/../Fixtures/open-meteo-weather.json');
        return json_decode($content, true);
    }

    private function createMockResponse(array $data): Response
    {
        $body = Mockery::mock(StreamInterface::class);
        $body->shouldReceive('getContents')
            ->andReturn(json_encode($data));

        $response = Mockery::mock(Response::class);
        $response->shouldReceive('getBody')
            ->andReturn($body);

        return $response;
    }

    private function createServiceWithMockClient(Client $mockClient): WeatherService
    {
        $service = new WeatherService();

        $reflection = new \ReflectionClass($service);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($service, $mockClient);

        return $service;
    }


    public function test_get_weather_returns_weather_object(): void
    {
        $fixture = $this->getFixture();
        $mockResponse = $this->createMockResponse($fixture);
        $mockPromise = P\Create::promiseFor($mockResponse);
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('getAsync')->andReturn($mockPromise);

        $service = $this->createServiceWithMockClient($mockClient);
        $result = $service->getWeather(45.4642, 9.1900)->wait();

        $this->assertInstanceOf(Weather::class, $result);
        $this->assertEquals(5.2, $result->temperature);
        $this->assertEquals('Europe/Rome', $result->timezone);
    }
}

