<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\drink;
use App\Models\establishment;
use App\Models\inventoryDrink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class productController extends Controller
{
    //
    //Get all product
    public function index(Request $request) 
    {
        try {   

            $establishmnts = establishment::all();

            foreach ($establishmnts as $establishmnt) {

                $index = array_search($request->user()->id, json_decode($establishmnt->workers));

                if ($index !== false) {

                    $inventoryDrink  = inventoryDrink::where('establishment_id', $establishmnt->id)
                                            ->where('quantity', '>', 0)
                                            ->join('drinks', 'inventory_drinks.drink_id', '=', 'drinks.id')
                                            ->get();

                    return response()->json([
                        'error'=>false,
                        'message'=> 'Data received successfully', 
                        'data'=>$inventoryDrink
                    ], 200);
                }
            }
            return response()->json([
                'error'=>false,
                'message'=> "You're not authorized to get this ressource",
            ], 400); 


        } catch (Throwable $e) {
            return response()->json([
                'error'=>true,
                'message' => 'Request failed, please try again',
                'data' => $e,
            ], 400);
        }
    }
    

    //Get all products
    public function allProducts(Request $request) 
    {
        try {   

            $drinks  = drink::get();

            return response()->json([
                'error'=>false,
                'message'=> 'Data received successfully', 
                'data'=>$drinks
            ], 200);

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
            
            DB::beginTransaction();
            
            $establishmnt = establishment::where('user_id',$request->user()->id)->first();

            if($establishmnt->user_id == $request->user()->id){
                
                $inventoryDrinkList = inventoryDrink::where('establishment_id', $establishmnt->id)->get();
                
                $products_created = [];

                foreach ($request->drinkList as $drink) {
                    foreach ($inventoryDrinkList as $inventoryDrink) {
                        if($drink['drink_id'] == $inventoryDrink->drink_id){
                            return response()->json([
                                'error'=>false,
                                'message'=> 'This product has been created, please delete it or update that', 
                                'data'=>$drink
                            ], 400);
                        }                        
                    }

                    $product = inventoryDrink::create([
                        'quantity' => (integer) $drink['quantity'],
                        'price' => (double) $drink['price'],
                        'drink_id' => (integer) $drink['drink_id'],
                        'establishment_id' => $establishmnt->id,
                    ]);
                    array_push($products_created, $product);
                }              

                DB::commit();
                return response()->json([
                    'error'=>false,
                    'message'=> 'Products created successfully', 
                    'data'=>$products_created
                ], 200); 

            }else {
                return response()->json([
                    'error'=>false,
                    'message'=> "You're not authorized to create this ressource",
                ], 400); 
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
    
    
    /**
     * Procurement a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function procurement(Request $request)
    {
        try {
            
            DB::beginTransaction();
            
            $establishmnt = establishment::where('user_id',$request->user()->id)->first();

            if($establishmnt->user_id == $request->user()->id){
                
                $inventoryDrinkList = inventoryDrink::where('establishment_id', $establishmnt->id)->get();

                $productsUpdated = [];

                foreach ($request->drinkList as $drink) {
                    foreach ($inventoryDrinkList as $inventoryDrink) {
                        if($drink['drink_id'] == $inventoryDrink->drink_id){

                            $inventoryDrink->quantity += (integer) $drink['quantity'];
                            $inventoryDrink->save();
                            
                            array_push($productsUpdated, $inventoryDrink);

                        }                        
                    }
                }              

                DB::commit();
                return response()->json([
                    'error'=>false,
                    'message'=> 'Products updated successfully', 
                    'data'=>$productsUpdated
                ], 200); 

            }else {
                return response()->json([
                    'error'=>false,
                    'message'=> "You're not authorized to create this ressource",
                ], 400); 
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
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function product(Request $request)
    {
        try {
            
                
            DB::beginTransaction();
            $establishmnt = establishment::where('user_id',$request->user()->id)->first();

            if($establishmnt->user_id == $request->user()->id){
                
                $inventoryDrinkList = inventoryDrink::where('establishment_id', $establishmnt->id)->get();
                
                foreach ($inventoryDrinkList as $inventoryDrink) {
                    if($request->drink_id == $inventoryDrink->drink_id){
                        return response()->json([
                            'error'=>false,
                            'message'=> 'This product has been created, please delete it or update that', 
                            'data'=>$inventoryDrink
                        ], 400);
                    }                        
                }
                $product = inventoryDrink::create([
                    'quantity' => (integer) $request->quantity,
                    'price' => (double) $request->price,
                    'drink_id' => (integer) $request->drink_id,
                    'establishment_id' => $establishmnt->id,
                ]);                

                DB::commit();

                return response()->json([
                    'error'=>false,
                    'message'=> 'Product created successfully', 
                    'data'=>$product
                ], 200); 

            }else {
                return response()->json([
                    'error'=>false,
                    'message'=> "You're not authorized to create this ressource",
                ], 400); 
            }
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error'=>true,
                'message' => 'Request failed, please try again',
                'data' => $th,
            ], 400);        
        }

    }
}
