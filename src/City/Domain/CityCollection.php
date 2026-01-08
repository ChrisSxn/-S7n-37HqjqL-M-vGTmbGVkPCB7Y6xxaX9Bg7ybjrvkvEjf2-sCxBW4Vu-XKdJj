<?php

namespace Woub\City\Domain;

use ChrisSercan\ValueObject\ValueObjectCollection;

/**
 * @extends ValueObjectCollection<City>
 */
final class CityCollection extends ValueObjectCollection
{
    protected function __construct(City ...$items)
    {
    }
}

