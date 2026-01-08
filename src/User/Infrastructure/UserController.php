<?php

namespace Woub\User\Infrastructure;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Woub\User\Application\Actions\AuthenticateUser;

final class UserController
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        dispatch_sync(new AuthenticateUser($request->email, $request->password));

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
        ]);
    }

    public function user(Request $request): JsonResponse
    {
        if (!Auth::guard('web')->check()) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout successful',
        ]);
    }
}
