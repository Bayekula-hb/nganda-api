<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\establishment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $establishmnts = establishment::all();
            $currentEstablishment = '';

            foreach ($establishmnts as $establishmnt) {
                $index = array_search($request->user()->id, json_decode($establishmnt->workers));
                if ($index !== false) {
                    $currentEstablishment = $establishmnt->id;
                }
            }

            $data = [
                'phone' =>  '243'. $request->input('phone'),
                'amount' => $request->input('amount'),
                'currency' => $request->input('currency'),
                'merchant' => 'JOSBARK',
                'reference' => $request->input('reference'),
                'callbackUrl' => 'https://backend.flexpay.cd/api/rest/',
                'type' => 1,
            ];

            $response = Http::
                            withHeaders([
                                'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJcL2xvZ2luIiwicm9sZXMiOlsiTUVSQ0hBTlQiXSwiZXhwIjoxNzQ2MTg4NDU1LCJzdWIiOiJmYWRhOTA5MDVmY2EzNDA1OWQzMmFjOWQ1ZWY3MWY1OCJ9.wQyYxNDRQlGPos8oIXrv1E3fJ3DoYWRYI6OQt_AszAc',
                            ])
                            ->post('https://backend.flexpay.cd/api/rest/v1/paymentService', $data);

            if ($response->successful()) {
                
                $payment = Payment::create([
                    'establishment_id' => (integer) $currentEstablishment,
                    'amount' => (double) $request->input('amount'),
                    'payment_method' => $request->input('type') == 1 ? 'mobile money' : 'carte bancaire' ,
                    'number_month' => 1,
                    'status' => 'processing',
                    'ref_flexpay' => $response->json('orderNumber'),
                ]);

                return response()->json([
                    'message' => $response->json('message'), 
                    'data' => $response->json(),
                    'payment' => $payment
                ]);
            } else {
                return response()->json([
                    'message' => 'Impossible d\'envoyer la requete, veuillez réesayer plustard',
                    'data' => []
                ], 500);
            } 
        } catch (\Throwable $th) {            
            return response()->json([
                'message' => 'Impossible d\'envoyer la requete, veuillez réesayer plustard',
                'error' => $th,
                'data' => []
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
                
            $payments = Payment::
                    // where('establishment_id', $id)->
                    get();

            return response()->json([
                'message' => 'Data received successfully', 
                'data' => $payments
            ]);
        } catch (\Throwable $th) {                        
            return response()->json([
                'message' => 'Impossible d\'envoyer la requete, veuillez réesayer plustard',
                'error' => $th,
                'data' => []
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
