<?php

namespace Woub\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

final class PageController extends Controller
{
    public function login(): Response|RedirectResponse
    {
        if (Auth::check()) {
            return redirect('/');
        }

        return Inertia::render('Login');
    }

    public function index(): Response
    {
        return Inertia::render('MainPage', [
            'view' => 'home',
        ]);
    }

    public function explore(): Response
    {
        return Inertia::render('MainPage', [
            'view' => 'explore',
        ]);
    }

    public function cities(): Response
    {
        return Inertia::render('MainPage', [
            'view' => 'favorites',
        ]);
    }
}

