<?php

namespace Woub\City\Domain;

use ChrisSercan\ValueObject\ValueObject;

final class CityDetailsEntity extends ValueObject
{
    public function __construct(
        public readonly CityDetails $cityDetails,
        public readonly int $city_id,
        public readonly ?string $cache_age = null
    ) {
    }

    public function toArray(): array
    {
        $data = $this->cityDetails->toArray();
        $data['city_id'] = $this->city_id;
        
        if ($this->cache_age !== null) {
            $data['cache_age'] = $this->cache_age;
        }
        
        return $data;
    }
}

