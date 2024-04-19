<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\drink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class drinkController extends Controller
{
    //Get all product
    public function index(Request $request) 
    {
        try {   

            $drinks = drink::all();

            return response()->json([
                'error'=>false,
                'message'=> 'Data received successfully', 
                'data'=>$drinks
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'error'=>true,
                'message' => 'Request failed, please try again',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

        
    /**
     * update a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            
            DB::beginTransaction();
            
            $drinks = drink::all();

            $productsUpdated = [];

            foreach ($request->drinkList as $drink) {
                foreach ($drinks as $currentDrink) {

                    if($drink['drink_id'] == $currentDrink->id){

                        // Stockez le fichier dans le sous-dossier "img" du répertoire "public"
                        $imagePath = $request->file('imageDrink')->store('img', 'public');

                        $currentDrink->imageDrink = $imagePath;
                        $currentDrink->save();
                        
                        array_push($productsUpdated, $currentDrink);   
                    }
                                            
                }
            }           

            DB::commit();
            return response()->json([
                'error'=>false,
                'message'=> 'Products updated successfully', 
                'data'=>$productsUpdated
            ], 200); 
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error'=>true,
                'message' => 'Request failed, please try again',
                'data' => $th->getMessage(),
            ], 400);        
        }
    }
}
