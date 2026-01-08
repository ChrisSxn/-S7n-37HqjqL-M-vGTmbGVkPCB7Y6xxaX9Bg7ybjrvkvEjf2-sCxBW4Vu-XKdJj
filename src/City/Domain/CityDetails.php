<?php

namespace Woub\City\Domain;

use ChrisSercan\ValueObject\ValueObject;

final class CityDetails extends ValueObject
{
    public function __construct(
        public readonly string $city,
        public readonly ?Image $image = null,
        public readonly ?Weather $weather = null,
        public readonly ?Currency $currency = null,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null
    ) {
    }
}

