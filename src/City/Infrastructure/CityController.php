<?php

namespace Woub\City\Infrastructure;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Woub\City\Application\Actions\FetchPhoto;
use Woub\City\Application\Actions\GetCitiesByName;
use Woub\City\Application\Actions\GetCityDetailsByName;
use Woub\City\Application\Actions\GetLikedCities;
use Woub\City\Application\Actions\LikeCity;
use Woub\City\Application\Actions\UnlikeCity;
use Woub\City\Domain\City;
use Woub\City\Domain\CityCollection;
use Woub\City\Domain\CityDetails;
use Woub\City\Domain\CityDetailsEntity;

final class CityController
{
    public function search(string $city): JsonResponse
    {
        /* @var CityCollection $cities */
        $cities = dispatch_sync(new GetCitiesByName($city));

        return response()->json([
            'status' => 'success',
            'message' => 'Cities retrieved successfully',
            'data' => $cities->toPlainArray()
        ]);
    }

    public function get(string $city): JsonResponse
    {
        $cityDetails = dispatch_sync(new GetCityDetailsByName($city));

        return response()->json([
            'status' => 'success',
            'message' => 'City details retrieved successfully',
            'data' => $cityDetails->toArray()
        ]);
    }

    public function like(Request $request): JsonResponse
    {
        $request->validate([
            'city_name' => 'required|string'
        ]);

        $city = dispatch_sync(new LikeCity(
            cityName: $request->input('city_name'),
            userId: (int) Auth::id()
        ));

        return response()->json([
            'status' => 'success',
            'message' => 'City liked successfully',
            'data' => $city->toArray()
        ]);
    }

    public function unlike(int $cityId): JsonResponse
    {
        dispatch_sync(new UnlikeCity(
            cityId: $cityId,
            userId: (int) Auth::id()
        ));

        return response()->json([
            'status' => 'success',
            'message' => 'City unliked successfully'
        ]);
    }

    public function index()
    {
        $userId = (int) Auth::id();

        return response()->stream(function () use ($userId): \Generator {
            $generator = dispatch_sync(new GetLikedCities(
                userId: $userId
            ));

            foreach ($generator as $cityDetails) {
                yield json_encode([
                    'status' => 'success',
                    'data' => $cityDetails->toArray()
                ]) . "\n";
            }
        }, 200, [
            'Content-Type' => 'application/x-ndjson',
            'X-Accel-Buffering' => 'no',
            'Cache-Control' => 'no-cache',
        ]);
    }

    public function proxyImage(string $photoPath): \Illuminate\Http\Response
    {
        $response = dispatch_sync(new FetchPhoto($photoPath));
        
        if ($response === null) {
            abort(404, 'Image not found');
        }

        return response($response->getBody()->getContents(), 200, [
            'Content-Type' => $response->getHeader('Content-Type')[0] ?? 'image/jpeg',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
