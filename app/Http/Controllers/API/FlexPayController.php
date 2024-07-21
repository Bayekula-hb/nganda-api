<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FlexPayController extends Controller
{
    public function sendData(Request $request)
    {
        $data = [
            'phone' =>  '243'. $request->input('phone'),
            'amount' => $request->input('amount'),
            'currency' => $request->input('currency'),
            'merchant' => 'JOSBARK',
            'reference' => $request->input('reference'),
            'callbackUrl' => 'https://backend.flexpay.cd/api/rest/',
            'type' => '1',
        ];

        $response = Http::
                        withHeaders([
                            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJcL2xvZ2luIiwicm9sZXMiOlsiTUVSQ0hBTlQiXSwiZXhwIjoxNzQ2MTg4NDU1LCJzdWIiOiJmYWRhOTA5MDVmY2EzNDA1OWQzMmFjOWQ1ZWY3MWY1OCJ9.wQyYxNDRQlGPos8oIXrv1E3fJ3DoYWRYI6OQt_AszAc',
                        ])
                        ->post('https://backend.flexpay.cd/api/rest/v1/paymentService', $data);

        if ($response->successful()) {
            // Traitez la réponse si nécessaire
            return response()->json(['success' => 'Data sent successfully']);
        } else {
            // Gérez les erreurs de la requête
            return response()->json(['error' => 'Unable to send data'], 500);
        }
    }
}
