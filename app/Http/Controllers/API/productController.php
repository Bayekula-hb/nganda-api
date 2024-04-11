<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\drink;
use App\Models\establishment;
use App\Models\inventoryDrink;
use App\Models\User;
use Illuminate\Http\Request;
use Throwable;

class productController extends Controller
{
    //
    //Get all product
    public function index(Request $request) 
    {




        try {   

            $establishmnt = establishment::where('user_id',$request->user()->id)->first();

            if($establishmnt){

                $inventoryDrink  = inventoryDrink::where('establishment_id', $establishmnt)
                                        ->join('drinks', 'inventory_drinks.drink_id', '=', 'drinks.id')
                                        ->get();

                return response()->json([
                    'error'=>false,
                    'message'=> 'Data received successfully', 
                    'data'=>$inventoryDrink
                ], 200);

                $drinks = drink::orderBy('id', 'desc')->get();

                return response()->json([
                    'error'=>false,
                    'message'=> 'Data received successfully', 
                    'data'=>$drinks
                ], 200);
            }

        } catch (Throwable $e) {
            return response()->json([
                'error'=>true,
                'message' => 'Request failed, please try again',
                'data' => $e,
            ], 400);
        }
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            
            $establishmnt = establishment::where('user_id',$request->user()->id)->first();

            if($establishmnt->user_id == $request->user()->id){

                foreach ($request->drinkList as $key => $drink) {

                    $products = inventoryDrink::create([
                        'quantity' => $drink->quantity,
                        'price' => $drink->price,
                        'drink_id' => $drink->drink_id,
                        'establishment_id' => $establishmnt->id,
                    ]);
                }
                return response()->json([
                    'error'=>false,
                    'message'=> 'Products created successfully', 
                    // 'data'=>$package
                ], 200); 

            }else {
                return response()->json([
                    'error'=>false,
                    'message'=> "You're not authorized to create this ressource",
                ], 400); 
            }
            
        } catch (Throwable $e) {
            return response()->json([
                'error'=>true,
                'message' => 'Request failed, please try again',
                'data' => $e,
            ], 400);        
        }
    }
}
