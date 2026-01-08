<?php

namespace Woub\City\Application\Actions;

use Psr\Http\Message\ResponseInterface;
use Woub\City\Application\Contracts\GoogleMapsServiceInterface;

final readonly class FetchPhoto
{
    public function __construct(
        private string $photoPath
    ) {
    }

    public function handle(GoogleMapsServiceInterface $googleMapsService): ?ResponseInterface
    {
        $photoName = base64_decode($this->photoPath, true);
        
        if ($photoName === false) {
            return null;
        }
        
        return $googleMapsService->fetchPhoto($photoName);
    }
}

