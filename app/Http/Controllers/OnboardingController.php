<?php

namespace App\Http\Controllers;

use App\Http\Requests\OnboardingStoreRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class OnboardingController extends Controller
{
    public function show(): Response|RedirectResponse
    {
        if (User::query()->exists()) {
            return redirect()->route('home');
        }

        return Inertia::render('onboarding');
    }

    public function store(OnboardingStoreRequest $request): RedirectResponse
    {
        if (User::query()->exists()) {
            return redirect()->route('home');
        }

        $name = Str::of($request->validated('name'))->squish()->toString();

        User::query()->create([
            'name' => $name,
            'email' => Str::uuid().'@sumie.test',
            'password' => Hash::make(Str::random(32)),
        ]);

        return redirect()->route('home');
    }
}
