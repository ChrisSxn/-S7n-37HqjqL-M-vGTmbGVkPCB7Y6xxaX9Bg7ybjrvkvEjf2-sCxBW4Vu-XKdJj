<?php

namespace Database\Seeders;

use Woub\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'woub@example.com',
            'password' => Hash::make('woubcity2026'),
            'email_verified_at' => now(),
        ]);
    }
}

