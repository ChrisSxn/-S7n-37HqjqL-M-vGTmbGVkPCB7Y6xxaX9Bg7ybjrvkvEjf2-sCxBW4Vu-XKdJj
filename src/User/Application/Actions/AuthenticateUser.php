<?php

namespace Woub\User\Application\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Woub\Models\User;

final readonly class AuthenticateUser
{
    public function __construct(
        private string $email,
        private string $password
    ) {
    }

    public function handle(): User
    {
        $user = User::where('email', $this->email)->first();

        if (!$user || !Hash::check($this->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        Auth::guard('web')->login($user, true);
        Session::regenerate();

        return $user;
    }
}

