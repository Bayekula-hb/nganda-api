<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class   signMiddleware
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
            'email' => ['required', 'email', 'unique:users,email'],
            'gender' => ['required', 'max:1', 'in:M,F'],
            'password' => ['required','min:6'],
            'nameEtablishment' => ['required', 'string','min:2', 'max:100'],
            'latitude' => ['string','min:2', 'max:20'],
            'longitude' => ['string','min:2', 'max:20'],
            'address' => ['required', 'string','min:2'],
            'pos' => ['string','min:2', 'max:100'],
            'numberPos' => ['string','min:2', 'max:100'],
            'workingDays' => ['required'],
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
