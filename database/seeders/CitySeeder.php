<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Woub\City\Application\Contracts\CityRepositoryInterface;
use Woub\City\Application\Contracts\GoogleMapsServiceInterface;
use Woub\Models\User;

class CitySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $cityRepository = app(CityRepositoryInterface::class);
        $adminUser = User::where('email', 'woub@example.com')->first();
        
        if (!$adminUser) {
            return;
        }

        $cityCoordinates = [
            'Amsterdam, Netherlands' => [52.3676, 4.9041],
            'Paris, France' => [48.8566, 2.3522],
            'London, UK' => [51.5074, -0.1278],
            'Berlin, Germany' => [52.5200, 13.4050],
            'Rome, Italy' => [41.9028, 12.4964],
            'Madrid, Spain' => [40.4168, -3.7038],
            'Barcelona, Spain' => [41.3851, 2.1734],
            'Vienna, Austria' => [48.2082, 16.3738],
            'Prague, Czech Republic' => [50.0755, 14.4378],
            'Budapest, Hungary' => [47.4979, 19.0402],
            'Warsaw, Poland' => [52.2297, 21.0122],
            'Stockholm, Sweden' => [59.3293, 18.0686],
            'Copenhagen, Denmark' => [55.6761, 12.5683],
            'Oslo, Norway' => [59.9139, 10.7522],
            'Helsinki, Finland' => [60.1699, 24.9384],
            'Dublin, Ireland' => [53.3498, -6.2603],
            'Lisbon, Portugal' => [38.7223, -9.1393],
            'Athens, Greece' => [37.9838, 23.7275],
            'Istanbul, Turkey' => [41.0082, 28.9784],
            'Moscow, Russia' => [55.7558, 37.6173],
            'Tokyo, Japan' => [35.6762, 139.6503],
            'Seoul, South Korea' => [37.5665, 126.9780],
            'Beijing, China' => [39.9042, 116.4074],
            'Shanghai, China' => [31.2304, 121.4737],
            'Hong Kong, China' => [22.3193, 114.1694],
            'Singapore, Singapore' => [1.3521, 103.8198],
            'Bangkok, Thailand' => [13.7563, 100.5018],
            'Kuala Lumpur, Malaysia' => [3.1390, 101.6869],
            'Jakarta, Indonesia' => [-6.2088, 106.8456],
            'Manila, Philippines' => [14.5995, 120.9842],
            'Mumbai, India' => [19.0760, 72.8777],
            'Delhi, India' => [28.6139, 77.2090],
            'Dubai, UAE' => [25.2048, 55.2708],
            'Cairo, Egypt' => [30.0444, 31.2357],
            'Johannesburg, South Africa' => [-26.2041, 28.0473],
            'Cape Town, South Africa' => [-33.9249, 18.4241],
            'Sydney, Australia' => [-33.8688, 151.2093],
            'Melbourne, Australia' => [-37.8136, 144.9631],
            'Auckland, New Zealand' => [-36.8485, 174.7633],
            'New York, USA' => [40.7128, -74.0060],
            'Los Angeles, USA' => [34.0522, -118.2437],
            'Chicago, USA' => [41.8781, -87.6298],
            'San Francisco, USA' => [37.7749, -122.4194],
            'Toronto, Canada' => [43.6532, -79.3832],
            'Vancouver, Canada' => [49.2827, -123.1207],
            'Mexico City, Mexico' => [19.4326, -99.1332],
            'São Paulo, Brazil' => [-23.5505, -46.6333],
            'Rio de Janeiro, Brazil' => [-22.9068, -43.1729],
            'Buenos Aires, Argentina' => [-34.6037, -58.3816],
            'Santiago, Chile' => [-33.4489, -70.6693],
            'Lima, Peru' => [-12.0464, -77.0428],
        ];

        foreach ($cityCoordinates as $cityName => $coords) {
            $cityRepository->persist($adminUser->id, $cityName, $coords[0], $coords[1]);
        }
    }
}

