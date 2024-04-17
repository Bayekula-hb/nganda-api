<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class receiverRegisterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validated = Validator::make($request->all(), [
            'firstName' => ['required', 'string','min:2', 'max:100'],
            'middleName' => ['min:2', 'string','max:100'],
            'lastName' => ['required', 'string','min:2', 'max:100'],
            'userName' => ['required', 'string','min:2', 'max:100'],
            'phoneNumber' => ['required', 'min:10', 'max:15'],
            'email' => ['email', 'unique:users,email'],
            'gender' => ['required', 'max:1', 'in:M,F'],
            'password' => ['required','min:6'],
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
