# City Explorer

Een app om steden te zoeken, bewaren en te bekijken.

Live demo: https://woub-city.criis.dev

## Requirements

- PHP 8.2+
- Node.js 20+
- Docker & Docker Compose
- Composer

## Setup

Clone de repository en installeer dependencies:

```bash
git clone <repository-url>
cd woub
composer install
npm install
```

Kopieer `.env.example` naar `.env` en voeg je API keys toe:

```
GOOGLE_MAPS_API_KEY=your_google_maps_api_key
MAPBOX_TOKEN=your_mapbox_token
```

Start Docker met Sail en run migrations:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate:fresh --seed
npm run build
```

De app draait op http://localhost. Default user: `woub@example.com` / `woubcity2026`.

## Architecture

De structuur is hexagonal architecture, geïmplementeerd per domain. Elke domain heeft zijn eigen bounded context. De domain layer bevat Value Objects zoals City, CityDetails, Weather en Image. De application layer bevat de business logic in actions, en de infrastructure layer implementeert externe dependencies zoals repositories en services. Dit zorgt voor losse koppeling en maakt het makkelijk om domains onafhankelijk te ontwikkelen en testen.

## Async Processing & Streaming

De `/api/cities` endpoint verwerkt steden concurrent in plaats van sequentieel. Er worden 3 steden tegelijk verwerkt, waarbij elke stad 2 externe API calls nodig heeft. Zodra een stad klaar is, wordt die direct naar de client gestreamd.

**Benchmark resultaten (53 steden):**

| Methode | Wanneer gebruiker iets ziet | Wanneer alles klaar is |
|---------|----------------------------|------------------------|
| Sequential (1 voor 1, geen streaming) | 19.3s | 19.3s |
| Async Concurrent (3 tegelijk, met streaming) | 1.0s | 7.0s |
| Async + Cache (met streaming) | 6ms | 21ms |

Stadsdetails worden gecached per city ID met 60 seconden TTL.

## Tech Stack

Backend: Laravel 12, Guzzle HTTP voor async requests, Inertia.js voor server-driven SPA, MySQL en Redis voor caching.
Frontend: Vue 3 met TypeScript, Tailwind CSS voor styling, Mapbox GL JS voor kaarten en Vite als build tool.

## Testing

Tests runnen met `./vendor/bin/sail artisan test`.
