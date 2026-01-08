<?php

namespace Woub\City\Domain;

use ChrisSercan\ValueObject\ValueObjectCollection;

/**
 * @extends ValueObjectCollection<CityDetails>
 */
final class CityDetailsCollection extends ValueObjectCollection
{
    protected function __construct(CityDetails ...$items)
    {
    }
}


