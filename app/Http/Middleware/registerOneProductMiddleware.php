<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class registerOneProductMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validated = Validator::make($request->all(), [
            'quantity' => ['required', 'integer'],
            'drink_id' => ['required', 'integer'],
            'price' => ['required', 'numeric'],
        ]);

        if($validated->fails()){
            return response()->json([
                'error' => true,
                'message' => 'Please, you can check your data sending and retry',
                'error_message' => $validated->errors()
            ], 400);
        }

        return $next($request);
    }
}
