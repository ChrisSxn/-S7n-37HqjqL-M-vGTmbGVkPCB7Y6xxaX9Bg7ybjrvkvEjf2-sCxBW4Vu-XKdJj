<?php

namespace Woub\City\Domain;

use ChrisSercan\ValueObject\ValueObject;

final class Weather extends ValueObject
{
    public function __construct(
        public readonly float $temperature,
        public readonly string $description,
        public readonly string $icon,
        public readonly string $timezone,
        public readonly string $time,
        public readonly float $windspeed,
        public readonly int $winddirection,
        public readonly int $elevation,
        public readonly int $weathercode,
        public readonly int $is_day
    ) {
    }
}
