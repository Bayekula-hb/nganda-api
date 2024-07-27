<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\drink;
use App\Models\establishment;
use App\Models\historicInventoryDrink;
use App\Models\inventoryDrink;
use App\Models\sale;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class saleController extends Controller
{
    
    //Get all product
    public function index(Request $request) 
    {
        try {   

            $establishmnt = establishment::where('user_id',$request->user()->id)->first();

            if($establishmnt){

                $inventoryDrink  = inventoryDrink::where('establishment_id', $establishmnt->id)
                                        ->join('drinks', 'inventory_drinks.drink_id', '=', 'drinks.id')
                                        ->get();

                return response()->json([
                    'error'=>false,
                    'message'=> 'Data received successfully', 
                    'data'=>$inventoryDrink
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
                        
                        $saleAdvance = [];
                        $isFindMoreOne = false;
                        $saleAmount = 0;

                        for ($incrLength1 = 0; $incrLength1 < sizeof($sales) ; $incrLength1++) { 

                            for ($i=$incrLength1+1; $i < sizeof($sales) ; $i++) {
                                if($sales[$incrLength1]->drinkID == $sales[$i]->drinkID){
                                    $sales[$incrLength1]->quantity += $sales[$i]->quantity;
                                    $sales[$incrLength1]->amount += $sales[$i]->amount;

                                    $isFindMoreOne = true;
                                    $saleAmount += (integer) $sales[$incrLength1]->amount;
                                    array_push($saleAdvance, $sales[$incrLength1]);
                                }
                            }
                            if($isFindMoreOne == false){
                                array_push($saleAdvance, $sales[$incrLength1]);
                                $saleAmount += (integer) $sales[$incrLength1]->amount;
                            }
                        }

                        return response()->json([
                            'error'=>false,
                            'message' => 'Statistics received successfully',
                            'data'=> [
                                'products' =>  $saleAdvance,
                                'amoutSale' =>  $saleAmount,
                            ]
                           
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
       
    //Get Statistics By Date
    public function statisticByDate (Request $request, $startDate, $endDate) 
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
                            ->whereBetween('sales.created_at',  [$startDate, $endDate])
                            ->get();
                        
                        $saleAdvance = [];
                        $isFindMoreOne = false;
                        $saleAmount = 0;

                        for ($incrLength1 = 0; $incrLength1 < sizeof($sales) ; $incrLength1++) { 

                            for ($i=$incrLength1+1; $i < sizeof($sales) ; $i++) {
                                if($sales[$incrLength1]->drinkID == $sales[$i]->drinkID){
                                    $sales[$incrLength1]->quantity += $sales[$i]->quantity;
                                    $sales[$incrLength1]->amount += $sales[$i]->amount;

                                    $isFindMoreOne = true;
                                    $saleAmount += (integer) $sales[$incrLength1]->amount;
                                    array_push($saleAdvance, $sales[$incrLength1]);
                                }
                            }
                            if($isFindMoreOne == false){
                                array_push($saleAdvance, $sales[$incrLength1]);
                                $saleAmount += (integer) $sales[$incrLength1]->amount;
                            }
                        }
                        return response()->json([
                            'error'=>false,
                            'message' => 'Statistics received successfully',
                            'data'=> [
                                'products' =>  $saleAdvance,
                                'amoutSale' =>  $saleAmount,
                            ]
                           
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


    //Get Statistics End Date
    public function statisticEndDateWithSixPreviousDays (Request $request, $endDate) 
    {
        try {

            $dateSend = new DateTime($endDate);
            $date = Carbon::parse($dateSend); 

            $weekByDateSend = collect();
            for ($i = 0; $i <= 6; $i++) {
                $weekByDateSend->push($date->copy()->subDays($i)->toDateString());
            }

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

                        $dataSaleByWeek = [];
                        $dataSaleByWeekDetails = [];
                        $totalAmountSale = 0;

                        for ($i = sizeof($weekByDateSend) -1 ; $i >= 0 ; $i--) { 

                            $currentSale = drink::select(
                                'sales.id',
                                'sales.quantity',
                                'invt.id as inventoryID', 
                                'invt.price',
                                'drinks.nameDrink',
                                'drinks.id as drinkID',
                                DB::raw('sales.quantity * invt.price as amount')
                            )
                            ->join('inventory_drinks as invt', 'invt.drink_id', '=', 'drinks.id')
                            ->join('sales', 'invt.id', '=', 'sales.inventory_drink_id')
                            ->where('sales.establishment_id', $establishmnt->id)
                            ->whereBetween('sales.created_at',  [Carbon::parse($weekByDateSend[$i])->startOfDay(), Carbon::parse($weekByDateSend[$i])->endOfDay()])
                            ->get();

                            $tempData =  [
                                "currentDate" => $weekByDateSend[$i],
                                "data" => $currentSale
                            ];
                            array_push($dataSaleByWeek,$tempData);
                        }
                        
                        $saleAdvance = [];
                        $isFindMoreOne = false;

                        for ($incr=0; $incr < sizeof($dataSaleByWeek); $incr++) { 
                            $saleAmount = 0;
                            for ($incrLength1 = 0; $incrLength1 < sizeof($dataSaleByWeek[$incr]["data"]) ; $incrLength1++) { 
                                for ($i= $incrLength1+1; $i < sizeof($dataSaleByWeek[$incr]["data"]) ; $i++) {

                                    if($dataSaleByWeek[$incr]["data"][$incrLength1]->drinkID == $dataSaleByWeek[$incr]["data"][$i]->drinkID){
                                        $dataSaleByWeek[$incr]["data"][$incrLength1]->quantity += $dataSaleByWeek[$incr]["data"][$i]->quantity;
                                        $dataSaleByWeek[$incr]["data"][$incrLength1]->amount += $dataSaleByWeek[$incr]["data"][$i]->amount;

                                        $isFindMoreOne = true;
                                        $saleAmount += (integer) $dataSaleByWeek[$incr]["data"][$incrLength1]->amount;
                                        array_push($saleAdvance, $dataSaleByWeek[$incr]["data"][$incrLength1]);
                                    }
                                }
                                if($isFindMoreOne == false){
                                    array_push($saleAdvance, $dataSaleByWeek[$incr]["data"][$incrLength1]);
                                    $saleAmount += (integer) $dataSaleByWeek[$incr]["data"][$incrLength1]->amount;
                                }
                            }
                            $totalAmountSale += $saleAmount;
                            array_push($dataSaleByWeekDetails, [
                                "amount" => $saleAmount,
                                "currentDate" => $dataSaleByWeek[$incr]["currentDate"]
                            ]);
                        }
                        return response()->json([
                            'error'=>false,
                            'message' => 'Statistics received successfully',
                            'data'=> [$dataSaleByWeekDetails],
                            'chart'=> [$dataSaleByWeekDetails]
                           
                        ], 200);
                    }else {
                        return response()->json([
                            'error'=>false,
                            'message'=> "You're not authorized to get this ressource",
                        ], 400); 
                    }
                }
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
                                    
                                    $inventoryDrink->quantity -= $drink['quantity'];
                                    $inventoryDrink->save();                                    
                                                        
                                    historicInventoryDrink::create([                        
                                        'quantity' => (integer) $drink['quantity'],
                                        'price' => (double) $inventoryDrink->price,
                                        'drink_id' => (integer) $inventoryDrink->drink_id,
                                        'establishment_id' => $establishmnt->id,
                                        'type_operator' => 'output',
                                        'user_id' => $request->user()->id,
                                    ]); 


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
