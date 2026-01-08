<?php

namespace Woub\City\Domain;

use ChrisSercan\ValueObject\ValueObject;

final class Currency extends ValueObject
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $symbol
    ) {
    }
}

