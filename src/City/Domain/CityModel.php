<?php

namespace Woub\City\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Woub\Models\User;

final class CityModel extends Model
{
    protected $table = 'cities';

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'city_user', 'city_id', 'user_id')->withTimestamps();
    }
}

