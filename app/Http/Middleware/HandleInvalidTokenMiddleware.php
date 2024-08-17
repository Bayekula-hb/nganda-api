<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleInvalidTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (AuthenticationException $e) {
            throw new HttpResponseException(
                response()->json([
                    'error' => true,
                    'message' => 'Invalid or missing token',
                ], 401)
            );
        }
    }
}
