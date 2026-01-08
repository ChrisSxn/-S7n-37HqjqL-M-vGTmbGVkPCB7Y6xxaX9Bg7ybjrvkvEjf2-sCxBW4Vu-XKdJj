<?php

namespace Woub\City\Domain;

use ChrisSercan\ValueObject\ValueObject;

final class Image extends ValueObject
{
    public function __construct(
        public readonly string $url
    ) {
    }
}

