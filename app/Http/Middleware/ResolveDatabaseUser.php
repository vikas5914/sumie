<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ResolveDatabaseUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            $user = User::query()->oldest('id')->first();

            if ($user !== null) {
                Auth::setUser($user);
                $request->setUserResolver(static fn (): User => $user);
            }
        }

        if ($user === null && ! $request->routeIs('onboarding', 'onboarding.store')) {
            return redirect()->route('onboarding');
        }

        if ($user !== null && $request->routeIs('onboarding', 'onboarding.store')) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
