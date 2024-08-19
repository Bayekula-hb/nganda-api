<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\drink;
use App\Models\establishment;
use App\Models\historicInventoryDrink;
use App\Models\historicInventoryStore;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class historicInventoryDrinkController extends Controller
{
    
    //Get all product
    public function index(Request $request, $current_page) 
    {
        try {   

            $current_page = $current_page > 0 ? $current_page : 1;
            $establishmnt = establishment::where('user_id',$request->user()->id)->first();

            if($establishmnt){

                $historicInventoryDrink  = historicInventoryDrink::where('establishment_id', $establishmnt->id)
                                        ->join('drinks', 'historic_inventory_drinks.drink_id', '=', 'drinks.id')
                                        ->join('users', 'historic_inventory_drinks.user_id', '=', 'users.id')
                                        ->orderBy('historic_inventory_drinks.id', 'desc')
                                        ->paginate(50, ['*'], 'page', $current_page);

                return response()->json([
                    'error'=>false,
                    'message'=> 'Data received successfully', 
                    'data'=>$historicInventoryDrink
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
    
    //Get all product
    public function storeIndex(Request $request, $current_page) 
    {
        try {   

            $current_page = $current_page > 0 ? $current_page : 1;
            $establishmnt = establishment::where('user_id',$request->user()->id)->first();

            if($establishmnt){

                $historicInventoryStore  = historicInventoryStore::where('establishment_id', $establishmnt->id)
                                        ->join('drinks', 'historic_inventory_stores.drink_id', '=', 'drinks.id')
                                        ->join('users', 'historic_inventory_stores.user_id', '=', 'users.id')
                                        ->orderBy('historic_inventory_stores.id', 'desc')
                                        ->paginate(50, ['*'], 'page', $current_page);

                return response()->json([
                    'error'=>false,
                    'message'=> 'Data received successfully', 
                    'data'=>$historicInventoryStore
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

                    if($user->nameRole == 'admin' || $user->nameRole == 'manager' || $user->nameRole == 'barman' || $user->nameRole == 'cashier' || $user->nameRole == 'store-manager'){
                        

                        $sales = drink::select(
                                'sales.id',
                                'sales.quantity',
                                'invt.id as inventoryID', 
                                'invt.price', // Sélectionnez le prix de la boisson
                                'drinks.nameDrink',
                                'drinks.id as drinkID',
                                DB::raw('sales.quantity * invt.price as amount') // Calculez le montant
                            )
                            ->join('historic_inventory_drinks as invt', 'invt.drink_id', '=', 'drinks.id')
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
    
    //Get statistics 
    public function statisticsInStore (Request $request) 
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

                    if($user->nameRole == 'admin' || $user->nameRole == 'manager' || $user->nameRole == 'barman' || $user->nameRole == 'cashier' || $user->nameRole == 'store-manager'){
                        

                        $sales = drink::select(
                                'sale_stores.id',
                                'sale_stores.quantity',
                                'invt.id as inventoryID', 
                                'invt.price', // Sélectionnez le prix de la boisson
                                'drinks.nameDrink',
                                'drinks.id as drinkID',
                                DB::raw('sale_stores.quantity * invt.price as amount') // Calculez le montant
                            )
                            ->join('historic_inventory_stores as invt', 'invt.drink_id', '=', 'drinks.id')
                            ->join('sale_stores', 'invt.id', '=', 'sale_stores.inventory_drink_id')
                            ->where('sale_stores.establishment_id', $establishmnt->id)
                            ->whereBetween('sale_stores.created_at',  [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])
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
                            ->join('historic_inventory_drinks as invt', 'invt.drink_id', '=', 'drinks.id')
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
       
    //Get Statistics By Date
    public function statisticByDateInStore (Request $request, $startDate, $endDate) 
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
                                'sale_stores.id',
                                'sale_stores.quantity',
                                'invt.id as inventoryID', 
                                'invt.price', // Sélectionnez le prix de la boisson
                                'drinks.nameDrink',
                                'drinks.id as drinkID',
                                DB::raw('sale_stores.quantity * invt.price as amount') // Calculez le montant
                            )
                            ->join('inventory_stores as invt', 'invt.drink_id', '=', 'drinks.id')
                            ->join('sale_stores', 'invt.id', '=', 'sale_stores.inventory_drink_id')
                            ->where('sale_stores.establishment_id', $establishmnt->id)
                            ->whereBetween('sale_stores.created_at',  [$startDate, $endDate])
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
    public function statisticInIntervaleByDate (Request $request) 
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

                    if($user->nameRole == 'admin' || $user->nameRole == 'manager'){
                        
                        $sales = drink::select(
                                'sales.id',
                                'sales.quantity',
                                'sales.user_id as userID',
                                'invt.id as inventoryID', 
                                'invt.price', // Sélectionnez le prix de la boisson
                                'drinks.nameDrink',
                                'drinks.id as drinkID',
                                DB::raw('sales.quantity * invt.price as amount') // Calculez le montant
                            )
                            ->join('inventory_drinks as invt', 'invt.drink_id', '=', 'drinks.id')
                            ->join('sales', 'invt.id', '=', 'sales.inventory_drink_id')
                            ->where('sales.establishment_id', $establishmnt->id)
                            ->where('sales.user_id', $request->user_id)
                            ->whereBetween('sales.created_at',  [$request->startDate, $request->endDate])
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
    public function statisticInIntervaleByDateInStore (Request $request) 
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

                    if($user->nameRole == 'admin' || $user->nameRole == 'manager'){
                        
                        $sales = drink::select(
                                'sale_stores.id',
                                'sale_stores.quantity',
                                'sale_stores.user_id as userID',
                                'invt.id as inventoryID', 
                                'invt.price', // Sélectionnez le prix de la boisson
                                'drinks.nameDrink',
                                'drinks.id as drinkID',
                                DB::raw('sale_stores.quantity * invt.price as amount') // Calculez le montant
                            )
                            ->join('inventory_stores as invt', 'invt.drink_id', '=', 'drinks.id')
                            ->join('sale_stores', 'invt.id', '=', 'sale_stores.inventory_drink_id')
                            ->where('sale_stores.establishment_id', $establishmnt->id)
                            ->where('sale_stores.user_id', $request->user_id)
                            ->whereBetween('sale_stores.created_at',  [$request->startDate, $request->endDate])
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


    //Get Statistics End Date
    public function statisticEndDateWithSixPreviousDaysInStore (Request $request, $endDate) 
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

                    if($user->nameRole == 'admin' || $user->nameRole == 'manager' || $user->nameRole == 'barman' || $user->nameRole == 'cashier' || $user->nameRole == 'store-manager'){

                        $dataSaleByWeek = [];
                        $dataSaleByWeekDetails = [];
                        $totalAmountSale = 0;

                        for ($i = sizeof($weekByDateSend) -1 ; $i >= 0 ; $i--) { 

                            $currentSale = drink::select(
                                'sale_stores.id',
                                'sale_stores.quantity',
                                'invt.id as inventoryID', 
                                'invt.price',
                                'drinks.nameDrink',
                                'drinks.id as drinkID',
                                DB::raw('sale_stores.quantity * invt.price as amount')
                            )
                            ->join('inventory_stores as invt', 'invt.drink_id', '=', 'drinks.id')
                            ->join('sale_stores', 'invt.id', '=', 'sale_stores.inventory_drink_id')
                            ->where('sale_stores.establishment_id', $establishmnt->id)
                            ->whereBetween('sale_stores.created_at',  [Carbon::parse($weekByDateSend[$i])->startOfDay(), Carbon::parse($weekByDateSend[$i])->endOfDay()])
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

}
