<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\drink;
use App\Models\establishment;
use App\Models\historicInventoryDrink;
use App\Models\inventoryDrink;
use App\Models\sale;
use App\Models\User;
use Illuminate\Http\Request;
use Throwable;

class adminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $current_page)
    {       

        try {
            $current_page = $current_page > 0 ? $current_page : 1;
            
            $establishments = establishment::paginate(50, ['*'], 'page', $current_page);

            return response()->json([
                'error'=>false,
                'message'=> 'Data received successfully', 
                'data'=>$establishments
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'error'=>true,
                'message' => 'Request failed, please try again',
                'data' => $e->getMessage(),
            ], 400);
        }
    }

    public function establishment(Request $request, $id)
    {       

        try {
            
            $establishment = establishment::where("id", $id)->first();
            $workers = [];
            $inventoryDrinks = [];
            $historicInventoryDrinks = [];
            $sales = [];

            if($establishment) {
                $user = User::where("id", $establishment->user_id)->first();
                $workersArray = (array) json_decode($establishment->workers, true);
                foreach($workersArray as $worker) {
                    $worker_find = User::where("id", $worker)->first();
                    array_push($workers, $worker_find);
                }
                $inventoryDrinks = inventoryDrink::where("establishment_id", $establishment->id)
                                                    ->join('drinks', 'inventory_drinks.drink_id', '=', 'drinks.id')
                                                    ->get();

                $historicInventoryDrinks = historicInventoryDrink::where("establishment_id", $establishment->id)
                                                    ->join('drinks', 'historic_inventory_drinks.drink_id', '=', 'drinks.id')
                                                    ->select(
                                                        'historic_inventory_drinks.id as historic_inventory_drinks_id',
                                                        'historic_inventory_drinks.quantity as historic_inventory_drinks_quantity',
                                                        'historic_inventory_drinks.price as historic_inventory_drinks_price',
                                                        'historic_inventory_drinks.created_at as historic_inventory_drinks_created_at',
                                                        'historic_inventory_drinks.type_operator as historic_inventory_drinks_type_operator',
                                                        'historic_inventory_drinks.type_operator as historic_inventory_drinks_type_operator',
                                                        'drinks.id as drink_id',
                                                        'drinks.nameDrink as nameDrink',
                                                        'drinks.typeDrink as typeDrink',
                                                    )
                                                    ->get();
                $sales = sale::where("sales.establishment_id", $establishment->id)
                                    ->join('inventory_drinks', 'sales.inventory_drink_id', '=', 'inventory_drinks.id')
                                    ->join('drinks', 'inventory_drinks.drink_id', '=', 'drinks.id')
                                    ->orderBy('sales.id', 'desc')
                                    ->select('sales.id as sale_id',
                                             'sales.quantity as sale_quantity',
                                             'sales.establishment_id as establishment_id',
                                             'sales.created_at as sale_created_at',
                                             'drinks.id as drink_id',
                                             'drinks.nameDrink as nameDrink',
                                             'drinks.typeDrink as typeDrink',
                                             'inventory_drinks.id as inventory_drink_id',
                                             'inventory_drinks.price as drinks.price',
                                            )
                                    ->get();                                               
            }

            return response()->json([
                'error'=>false,
                'message'=> 'Data received successfully', 
                'data'=> [
                    'etablishment' => $establishment,
                    'user' => $user,
                    'workers' => $workers,
                    'inventoryDrinks' => $inventoryDrinks,
                    'sales' => $sales,
                    'historicInventoryDrinks' => $historicInventoryDrinks,
                ]
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'error'=>true,
                'message' => 'Request failed, please try again',
                'data' => $e->getMessage()
            ], 400);
        }
    }

    public function statistics(Request $request)
    {       

        try {
            
            $establishment = establishment::get();
            $drinks = drink::get();
            $users = User::get();
            $sales = sale::get();

            $saleProducts = sale::join('inventory_drinks', 'sales.inventory_drink_id', '=', 'inventory_drinks.id')
                                    ->join('drinks', 'inventory_drinks.drink_id', '=', 'drinks.id')
                                    ->orderBy('sales.id', 'desc')
                                    ->select(   
                                                'sales.id as sale_id',
                                                'sales.quantity as sale_quantity',
                                                'sales.establishment_id as establishment_id',
                                                'sales.created_at as sale_created_at',
                                                'drinks.id as drink_id',
                                                'drinks.nameDrink as nameDrink',
                                                'drinks.typeDrink as typeDrink',
                                                'inventory_drinks.id as inventory_drink_id',
                                                'inventory_drinks.price as drinks.price',
                                            )
                                    ->get();                                               
            
            $historicInventoryDrinks = historicInventoryDrink::join('drinks', 'historic_inventory_drinks.drink_id', '=', 'drinks.id')
                            ->join('establishments', 'historic_inventory_drinks.establishment_id', '=', 'establishments.id')
                            ->orderBy('historic_inventory_drinks.id', 'desc')
                            ->select(
                                'historic_inventory_drinks.id as historic_inventory_drinks_id',
                                'historic_inventory_drinks.quantity as historic_inventory_drinks_quantity',
                                'historic_inventory_drinks.price as historic_inventory_drinks_price',
                                'historic_inventory_drinks.created_at as historic_inventory_drinks_created_at',
                                'historic_inventory_drinks.type_operator as historic_inventory_drinks_type_operator',
                                'historic_inventory_drinks.type_operator as historic_inventory_drinks_type_operator',
                                'drinks.id as drink_id',
                                'drinks.nameDrink as nameDrink',
                                'drinks.typeDrink as typeDrink',
                                'establishments.nameEtablishment as nameEtablishment',
                            )
                            ->get();
            
            return response()->json([
                'error'=>false,
                'message'=> 'Data received successfully', 
                'data'=> [
                    'etablishments' => count($establishment),
                    'users' => count($users),
                    'sales' => count($sales),
                    'drinks' => count($drinks),
                    'saleProducts' => $saleProducts,
                    'historicDrinks' => $historicInventoryDrinks,
                ]
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'error'=>true,
                'message' => 'Request failed, please try again',
                'data' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
