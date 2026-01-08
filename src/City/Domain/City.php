<?php

namespace Woub\City\Domain;

use ChrisSercan\ValueObject\ValueObject;

final class City extends ValueObject
{
    public function __construct(
        public readonly string $name,
        public readonly ?int $id = null,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null
    ) {
    }
}

