<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Traits\ResponsesTrait;
class CheckBanMiddleware
{
    use ResponsesTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('sanctum')->user();

        if ($user->banned_until && $user->banned_until > now()) {
            return $this->respondError(null, 'User is banned until ' . $user->banned_until);
        }

        return $next($request);
    }
}
