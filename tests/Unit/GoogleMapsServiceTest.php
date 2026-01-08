<?php

namespace Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise as P;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use Mockery;
use Psr\Http\Message\StreamInterface;
use Tests\TestCase;
use Woub\City\Domain\City;
use Woub\City\Domain\CityCollection;
use Woub\City\Domain\Image;
use Woub\City\Infrastructure\GoogleMapsService;

final class GoogleMapsServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function getFixture(string $path): array
    {
        $content = file_get_contents(__DIR__ . '/../Fixtures/google-maps-response.json');
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

    private function createServiceWithMockClient(Client $mockClient): GoogleMapsService
    {
        Config::set('services.google_maps.api_key', 'test-api-key');

        $service = new GoogleMapsService();

        $reflection = new \ReflectionClass($service);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($service, $mockClient);

        return $service;
    }

    public function test_search_returns_city_collection(): void
    {
        $fixture = $this->getFixture('google-maps-response.json');
        $mockResponse = $this->createMockResponse(['suggestions' => $fixture['autocomplete']['suggestions']]);
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('request')->andReturn($mockResponse);

        $service = $this->createServiceWithMockClient($mockClient);
        $result = $service->search('Milan');

        $this->assertInstanceOf(CityCollection::class, $result);
        $cities = $result->toPlainArray();
        $this->assertNotEmpty($cities);
        $this->assertEquals('Milan, Italy', $cities[0]['name']);
    }

    public function test_get_image_returns_image(): void
    {
        $fixture = $this->getFixture('google-maps-response.json');
        $mockResponse = $this->createMockResponse($fixture['searchText']['image']);
        $mockPromise = P\Create::promiseFor($mockResponse);
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('requestAsync')->andReturn($mockPromise);

        $service = $this->createServiceWithMockClient($mockClient);
        $result = $service->getImage('Milan')->wait();

        $this->assertInstanceOf(Image::class, $result);
        $this->assertStringStartsWith('/api/places/image/', $result->url);
    }

    public function test_get_coordinates_returns_array(): void
    {
        $fixture = $this->getFixture('google-maps-response.json');
        $mockResponse = $this->createMockResponse($fixture['searchText']['coordinates']);
        $mockPromise = P\Create::promiseFor($mockResponse);
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('requestAsync')->andReturn($mockPromise);

        $service = $this->createServiceWithMockClient($mockClient);
        $result = $service->getCoordinates('Milan')->wait();

        $this->assertIsArray($result);
        $this->assertEquals(45.4642, $result['latitude']);
        $this->assertEquals(9.1900, $result['longitude']);
    }
}

