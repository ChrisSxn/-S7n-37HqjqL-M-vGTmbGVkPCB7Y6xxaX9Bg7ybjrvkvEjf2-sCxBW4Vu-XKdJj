<?php

namespace Woub\City\Infrastructure;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Woub\City\Application\Contracts\GoogleMapsServiceInterface;
use Woub\City\Domain\City;
use Woub\City\Domain\CityCollection;
use Woub\City\Domain\Image;

final class GoogleMapsService implements GoogleMapsServiceInterface
{
    private Client $client;
    private string $apiKey;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://places.googleapis.com',
            'timeout' => 5,
        ]);
        $this->apiKey = (string) config('services.google_maps.api_key');

        if (empty($this->apiKey)) {
            throw new \RuntimeException('Google Maps API key is not configured');
        }
    }

    private function getHeaders(array $additional = []): array
    {
        return array_merge([
            'Content-Type' => 'application/json',
            'X-Goog-Api-Key' => $this->apiKey,
        ], $additional);
    }

    private function makeRequest(string $method, string $endpoint, array $headers = [], array $body = []): ?array
    {
        try {
            $response = $this->client->request($method, $endpoint, [
                'headers' => $this->getHeaders($headers),
                'json' => $body,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return null;
        }
    }

    private function makeAsyncRequest(string $endpoint, string $fieldMask, array $body): PromiseInterface
    {
        return $this->client->requestAsync('POST', $endpoint, [
            'headers' => $this->getHeaders(['X-Goog-FieldMask' => $fieldMask]),
            'json' => $body,
        ]);
    }

    private function mapSuggestionsToCities(array $suggestions): CityCollection
    {
        $cities = [];

        foreach ($suggestions as $suggestion) {
            $text = $suggestion['placePrediction']['text']['text'] ?? null;
            $types = $suggestion['placePrediction']['types'] ?? [];

            if (!$this->isValidCity($text, $types)) {
                continue;
            }

            $cities[] = new City($this->formatCityName($text));
        }

        return CityCollection::fromArray($cities);
    }

    private function isValidCity(?string $text, array $types): bool
    {
        if (empty($text)) {
            return false;
        }

        $validTypes = ['locality', 'administrative_area_level_1'];
        if (!array_intersect($validTypes, $types)) {
            return false;
        }

        $excludedPatterns = '/\b(airport|station|arena|stadium|mall|center|centre|boulevard|street|avenue|road|via|plaza)\b/i';
        if (preg_match($excludedPatterns, $text)) {
            return false;
        }

        return true;
    }

    private function formatCityName(string $text): ?string
    {
        $parts = array_map('trim', explode(',', $text));
        
        if (count($parts) >= 2) {
            $city = $parts[0];
            $country = end($parts);
            return $city . ', ' . $country;
        }
        
        return $text;
    }

    public function search(string $cityName): CityCollection
    {
        $response = $this->makeRequest('POST', '/v1/places:autocomplete', [
            'X-Goog-FieldMask' => 'suggestions.placePrediction.text,suggestions.placePrediction.placeId,suggestions.placePrediction.types',
        ], [
            'input' => $cityName,
            'includedPrimaryTypes' => ['locality', 'administrative_area_level_1'],
        ]);

        if (!$response || !isset($response['suggestions'])) {
            return CityCollection::fromArray([]);
        }

        return $this->mapSuggestionsToCities($response['suggestions']);
    }

    public function getImage(string $cityName): PromiseInterface
    {
        $promise = $this->makeAsyncRequest('/v1/places:searchText', 'places.photos', [
            'textQuery' => $cityName,
            'includedType' => 'locality',
            'maxResultCount' => 1,
        ]);

        return $promise->then(function ($response) {
            $data = json_decode($response->getBody()->getContents(), true);
            $photoName = $data['places'][0]['photos'][0]['name'] ?? null;

            if (empty($photoName)) {
                return null;
            }

            return new Image("/api/places/image/" . base64_encode($photoName));
        })->otherwise(function () {
            return null;
        });
    }

    public function getCoordinates(string $cityName): PromiseInterface
    {
        $promise = $this->makeAsyncRequest('/v1/places:searchText', 'places.location', [
            'textQuery' => $cityName,
            'includedType' => 'locality',
            'maxResultCount' => 1,
        ]);

        return $promise->then(function ($response) {
            $data = json_decode($response->getBody()->getContents(), true);
            $location = $data['places'][0]['location'] ?? null;

            if (empty($location)) {
                return null;
            }

            $latitude = $location['latitude'] ?? null;
            $longitude = $location['longitude'] ?? null;

            if ($latitude === null || $longitude === null) {
                return null;
            }

            return [
                'latitude' => (float) $latitude,
                'longitude' => (float) $longitude,
            ];
        })->otherwise(function () {
            return null;
        });
    }

    public function fetchPhoto(string $photoName): ?ResponseInterface
    {
        try {
            $url = "https://places.googleapis.com/v1/{$photoName}/media?key={$this->apiKey}&maxHeightPx=600&maxWidthPx=800";
            return $this->client->get($url);
        } catch (GuzzleException $e) {
            return null;
        }
    }
}
