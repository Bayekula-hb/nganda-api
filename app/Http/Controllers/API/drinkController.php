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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            
            DB::beginTransaction();
            
            return response()->json([
                'error'=>false,
                'message'=> 'Drink created with successfully', 
                'data'=>$request->drinkList
            ], 200); 

            $drinkCreated = [];

            foreach ($request->drinkList as $drink) {

                $drink = drink::create([
                    'nameDrink' => $request->nameDrink,
                    'litrage' => $request->litrage,
                    'typeDrink' => $request->typeDrink,
                ]);

                $drink->save();                
                array_push($drinkCreated, $drink); 
            }           

            DB::commit();
            return response()->json([
                'error'=>false,
                'message'=> 'Drink created with successfully', 
                'data'=>$drinkCreated
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

    /**
     * update a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateDrink(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $drink = drink::where('id', $request->drink_id)->first();
            if($drink){
                // Stockez le fichier dans le sous-dossier "img" du répertoire "public"
                $imagePath = $request->file('imageDrink')->store('img', 'public');

                $drink->imageDrink = $imagePath;
                $drink->save();
                
                DB::commit();
                return response()->json([
                    'error'=>false,
                    'message'=> 'Products updated successfully', 
                    'data'=>$drink
                ], 200);    
            }else {
                return response()->json([
                    'error'=>false,
                    'message'=> 'Products updated successfully', 
                    'data'=>$drink
                ], 200);
            }            
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
