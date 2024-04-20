<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\drink;
use App\Models\establishment;
use App\Models\inventoryDrink;
use App\Models\sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class saleController extends Controller
{
    
    //Get all product
    public function index(Request $request) 
    {
        try {   

            // $establishmnt = establishment::where('user_id',$request->user()->id)->first();

            // if($establishmnt){

            //     $inventoryDrink  = inventoryDrink::where('establishment_id', $establishmnt->id)
            //                             ->join('drinks', 'inventory_drinks.drink_id', '=', 'drinks.id')
            //                             ->get();

            //     return response()->json([
            //         'error'=>false,
            //         'message'=> 'Data received successfully', 
            //         'data'=>$inventoryDrink
            //     ], 200);
            // }

        } catch (Throwable $e) {
            return response()->json([
                'error'=>true,
                'message' => 'Request failed, please try again',
                'data' => $e,
            ], 400);
        }
    }
    
    //Get statistics 
    public function statistics (Request $request) 
    {
        try {
            
            DB::beginTransaction();

            $establishmnts = establishment::all();

            foreach ($establishmnts as $establishmnt) {
                $index = array_search($request->user()->id, json_decode($establishmnt->workers));
                if ($index !== false) {
                    
                    $user = User::where('users.id', $request->user()->id)
                                ->join('user_role_tabs', 'users.id', '=', 'user_role_tabs.user_id')
                                ->join('user_roles', 'user_roles.id', '=', 'user_role_tabs.user_role_id')                                
                                ->first();

                    if($user->nameRole == 'admin' || $user->nameRole == 'manager' || $user->nameRole == 'barman' || $user->nameRole == 'cashier'){
                        
                        // $sales = sale::select('inventory_drinks.id as inventoryDrinkId', 'inventory_drinks.establishment_id as establishmentDrinkId')
                        //         ->join('establishments', 'sales.establishment_id', '=', 'establishments.id')
                        //         ->join('inventory_drinks', 'sales.inventory_drink_id', '=', 'inventory_drinks.id')
                        //         ->groupBy('inventoryDrinkId')
                        //         ->selectRaw('inventory_drinks.id, COUNT(*) as count')
                        //         ->where('sales.establishment_id', $establishmnt->id)
                        //         ->whereNotBetween('sales.created_at',  [date('Y-m-d'), date('Y-m-d')])
                        //         ->get();

                        // $sales = sale::where('sales.establishment_id', $establishmnt->id)
                        //             ->join('establishments as est', 'sales.establishment_id', '=', 'est.id')
                        //             ->join('inventory_drinks as invt', 'est.id', '=', 'invt.establishment_id')
                        //             ->join('drinks', 'invt.drink_id', '=', 'drinks.id')
                        //             ->get();

                        // $sales = sale::select(
                        //         'sales.id',
                        //         'sales.quantity',
                        //         'invt.id as inventoryID', 
                        //         'invt.price', // Sélectionnez le prix de la boisson
                        //         'drinks.nameDrink',
                        //         'drinks.id as drinkID',
                        //         DB::raw('sales.quantity * invt.price as amount') // Calculez le montant
                        //     )
                        //     // ->join('establishments as est', 'sales.establishment_id', '=', 'est.id')
                        //     // ->join('inventory_drinks as invt', 'establishments.id', '=', 'invt.establishment_id')
                        //     ->join('inventory_drinks as invt', 'invt.id', '=', 'sales.inventory_drink_id')
                        //     ->join('drinks', 'invt.drink_id', '=', 'drinks.id')
                        //     ->where('sales.establishment_id', $establishmnt->id)
                        //     ->get();

                        $sales = drink::select(
                                'sales.id',
                                'sales.quantity',
                                'invt.id as inventoryID', 
                                'invt.price', // Sélectionnez le prix de la boisson
                                'drinks.nameDrink',
                                'drinks.id as drinkID',
                                DB::raw('sales.quantity * invt.price as amount') // Calculez le montant
                            )
                            ->join('inventory_drinks as invt', 'invt.drink_id', '=', 'drinks.id')
                            ->join('sales', 'invt.id', '=', 'sales.inventory_drink_id')
                            ->where('sales.establishment_id', $establishmnt->id)
                            ->whereBetween('sales.created_at',  [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])
                            ->get();

                        return response()->json([
                            'error'=>false,
                            'message' => 'Statistics received successfully',
                            'data'=>$sales
                        ], 200);  

                    }else {
                        return response()->json([
                            'error'=>false,
                            'message'=> "You're not authorized to get this ressource",
                        ], 400); 
                    }
                }
            }
            
            return response()->json([
                'error'=>true,
                'message' => 'Request failed, because your are not access to get statistics'
            ], 400);      
            
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
    public function store(Request $request)
    {
        try {
            
            DB::beginTransaction();

            // $establishmnt = establishment::where('id',  $request->user()->id)->first();
            $establishmnts = establishment::all();

            foreach ($establishmnts as $establishmnt) {

                $index = array_search($request->user()->id, json_decode($establishmnt->workers));
                
                if ($index !== false) {
                    
                    $user = User::where('users.id', $request->user()->id)
                                ->join('user_role_tabs', 'users.id', '=', 'user_role_tabs.user_id')
                                ->join('user_roles', 'user_roles.id', '=', 'user_role_tabs.user_role_id')                                
                                ->first();


                    if($user->nameRole == 'admin' || $user->nameRole == 'manager' || $user->nameRole == 'barman' || $user->nameRole == 'cashier'){
                        
                        $inventoryDrinkList = inventoryDrink::where('establishment_id', $establishmnt->id)->get();
                        
                        $productsSale = [];

                        foreach ($request->drinkList as $drink) {
                            foreach ($inventoryDrinkList as $inventoryDrink) {
                                if($drink['drink_id'] == $inventoryDrink->drink_id){
                                    $saleCreated = sale::create([
                                        'quantity' => (integer) $drink['quantity'],
                                        'user_id' => $request->user()->id,
                                        'inventory_drink_id' => $inventoryDrink->id,
                                        'establishment_id' => $establishmnt->id,
                                    ]);
                                    
                                    //Reduce the quantity after sale
                                    $inventoryDrink->quantity -= $drink['quantity'];
                                    $inventoryDrink->save();

                                    array_push($productsSale, $saleCreated);
                                }                        
                            }
                        }              

                        DB::commit();
                        return response()->json([
                            'error'=>false,
                            'message'=> 'Product(s) salle with successfully', 
                            'data'=> $productsSale
                        ], 200); 

                    }else {
                        return response()->json([
                            'error'=>false,
                            'message'=> "You're not authorized to create this ressource",
                        ], 400); 
                    }
                }
            }
            
            return response()->json([
                'error'=>true,
                'message' => 'Request failed, because your are not access to sale products'
            ], 400);      
            
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
