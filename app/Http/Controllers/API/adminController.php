<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\drink;
use App\Models\establishment;
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
            }

            return response()->json([
                'error'=>false,
                'message'=> 'Data received successfully', 
                'data'=> [
                    'etablishment' => $establishment,
                    'user' => $user,
                    'workers' => $workers,
                    'inventoryDrinks' => $inventoryDrinks,
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

            return response()->json([
                'error'=>false,
                'message'=> 'Data received successfully', 
                'data'=> [
                    'etablishments' => count($establishment),
                    'users' => count($users),
                    'sales' => count($sales),
                    'drinks' => count($drinks),
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
