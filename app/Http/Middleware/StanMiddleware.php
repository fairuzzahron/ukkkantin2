<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StanMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Get authenticated user
        $user = Auth::parseToken()->authenticate();

        // Check if the user is a stan
        if (!$user || $user->role !== 'stan') {
            return response()->json(['message' => 'Access denied. Only Stan users can perform this action.'], 403);
        }

        return $next($request);
    }
}
