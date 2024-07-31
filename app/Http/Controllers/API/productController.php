<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\drink;
use App\Models\establishment;
use App\Models\historicInventoryDrink;
use App\Models\historicInventoryStore;
use App\Models\inventoryDrink;
use App\Models\inventoryStore;
use App\Models\User;
use App\Models\userRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class productController extends Controller
{
    //
    //Get all product
    public function index(Request $request, $current_page) 
    {
        try {   
            
            $current_page = $current_page > 0 ? $current_page : 1;
            $establishmnts = establishment::all();

            foreach ($establishmnts as $establishmnt) {

                $index = array_search($request->user()->id, json_decode($establishmnt->workers));

                if ($index !== false) {

                    $inventoryDrink  = inventoryDrink::where('establishment_id', $establishmnt->id)
                                            ->join('drinks', 'inventory_drinks.drink_id', '=', 'drinks.id')
                                            ->paginate(50, ['*'], 'page', $current_page);

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

    //Get all products
    public function allProductsInStore(Request $request, $current_page) 
    {
        try {   

            $establishmnts = establishment::all();            
            $current_page = $current_page > 0 ? $current_page : 1;

            foreach ($establishmnts as $establishmnt) {

                $index = array_search($request->user()->id, json_decode($establishmnt->workers));


                if ($index !== false) {
                    
                    $userRoleTab = DB::table('users')
                        ->join('user_role_tabs', 'users.id', '=', 'user_role_tabs.user_id')
                        ->where('user_id', json_decode($establishmnt->workers)[$index])
                        ->first();

                    $userRole = userRole::where('id',$userRoleTab->user_role_id)
                                        ->get();
                    foreach($userRole as $role){

                        if($role->nameRole == "manager" || "store-manager" || "admin"){

                            $inventoryStore  = inventoryStore::where('establishment_id', $establishmnt->id)
                                                    ->join('drinks', 'inventory_stores.drink_id', '=', 'drinks.id')
                                                    ->paginate(50, ['*'], 'page', $current_page);

                            return response()->json([
                                'error'=>false,
                                'message'=> 'Data received successfully', 
                                'data'=>$inventoryStore
                            ], 200);
                        }
                    }                    
                    return response()->json([
                        'error'=>false,
                        'message'=> "You're not authorized to get this ressource",
                    ], 400);
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

            $userRoleTab = DB::table('users')
                ->join('user_role_tabs', 'users.id', '=', 'user_role_tabs.user_id')
                ->where('user_id',$request->user()->id)
                ->first();

            $userRole = userRole::where('id',$userRoleTab->user_role_id)
                ->first();

            if($establishmnt && $userRole->nameRole == "manager" || "barman"){
                
                $inventoryDrinkList = inventoryDrink::where('establishment_id', $establishmnt->id)->get();
                $inventoryDrinkHistorics = historicInventoryDrink::where('establishment_id', $establishmnt->id)->get();
                
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
                    historicInventoryDrink::create([                        
                        'quantity' => (integer) $drink['quantity'],
                        'price' => (double) $drink['price'],
                        'drink_id' => (integer) $drink['drink_id'],
                        'establishment_id' => $establishmnt->id,
                        'type_operator' => 'input',
                        'user_id' => $request->user()->id,
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeToInventoryStore(Request $request)
    {
        try {
            
            DB::beginTransaction();

            $establishmnts = establishment::all();
            $user = $request->user();
            $userId = $user->id;

            foreach ($establishmnts as $establishment) {
                $workers = json_decode($establishment->workers); // Décode le JSON des travailleurs
                
                if (in_array($userId, $workers)) {
                    
                    $userRoleTab = DB::table('users')
                        ->join('user_role_tabs', 'users.id', '=', 'user_role_tabs.user_id')
                        ->join('user_roles', 'user_roles.id', '=', 'user_role_tabs.user_role_id')
                        ->where('user_id',$request->user()->id)
                        ->get();

                    foreach($userRoleTab as $role){

                        if($role->nameRole == "manager" || "store-manager" || "admin"){
                            
                            $inventoryDrinkList = inventoryStore::where('establishment_id', $establishment->id)->get();
                            
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

                                $drinks = drink::where('id', $drink['drink_id'])->first();

                                if($drinks){
                                    $product = inventoryStore::create([
                                        'quantity' => (integer) $drink['quantity'],
                                        'price' => (double) $drink['price'],
                                        'drink_id' => (integer) $drink['drink_id'],
                                        'establishment_id' => $establishment->id,
                                    ]);
                                    historicInventoryStore::create([                        
                                        'quantity' => (integer) $drink['quantity'],
                                        'price' => (double) $drink['price'],
                                        'drink_id' => (integer) $drink['drink_id'],
                                        'establishment_id' => $establishment->id,
                                        'type_operator' => 'input',
                                        'user_id' => $request->user()->id,
                                    ]);
                                    array_push($products_created, $product);
                                }
                            }              

                            if(count($products_created) > 0){

                                DB::commit();
                                return response()->json([
                                    'error'=>false,
                                    'message'=> 'Products created successfully in store', 
                                    'data'=>$products_created
                                ], 200); 
                            }else {
                                DB::rollBack();
                                return response()->json([
                                    'error'=>false,
                                    'message'=> 'No Product created', 
                                    'data'=>$products_created
                                ], 200); 
                            }

                        }else {
                            return response()->json([
                                'error'=>false,
                                'message'=> "You're not authorized to create this ressource",
                            ], 400); 
                        }
                    }
                }
            }
            // return json_decode($establishmnts);
            // foreach ($establishmnts as $establishmnt) {

            //     $index = array_search($request->user()->id, json_decode($establishmnt->workers));

            //     if ($index !== false) {
                    
            //         $userRoleTab = DB::table('users')
            //             ->join('user_role_tabs', 'users.id', '=', 'user_role_tabs.user_id')
            //             ->join('user_roles', 'user_roles.id', '=', 'user_role_tabs.user_role_id')
            //             ->where('user_id',$request->user()->id)
            //             ->get();

            //         foreach($userRoleTab as $role){

            //             if($role->nameRole == "manager" || "store-manager" || "admin"){
                            
            //                 $inventoryDrinkList = inventoryStore::where('establishment_id', $establishmnt->id)->get();
                            
            //                 $products_created = [];

            //                 foreach ($request->drinkList as $drink) {
            //                     foreach ($inventoryDrinkList as $inventoryDrink) {

            //                         if($drink['drink_id'] == $inventoryDrink->drink_id){
            //                             return response()->json([
            //                                 'error'=>false,
            //                                 'message'=> 'This product has been created, please delete it or update that', 
            //                                 'data'=>$drink
            //                             ], 400);
            //                         }                        
            //                     }

            //                     $drinks = drink::where('id', $drink['drink_id'])->first();

            //                     if($drinks){
            //                         $product = inventoryStore::create([
            //                             'quantity' => (integer) $drink['quantity'],
            //                             'price' => (double) $drink['price'],
            //                             'drink_id' => (integer) $drink['drink_id'],
            //                             'establishment_id' => $establishmnt->id,
            //                         ]);
            //                         historicInventoryStore::create([                        
            //                             'quantity' => (integer) $drink['quantity'],
            //                             'price' => (double) $drink['price'],
            //                             'drink_id' => (integer) $drink['drink_id'],
            //                             'establishment_id' => $establishmnt->id,
            //                             'type_operator' => 'input',
            //                             'user_id' => $request->user()->id,
            //                         ]);
            //                         array_push($products_created, $product);
            //                     }
            //                 }              

            //                 if(count($products_created) > 0){

            //                     DB::commit();
            //                     return response()->json([
            //                         'error'=>false,
            //                         'message'=> 'Products created successfully in store', 
            //                         'data'=>$products_created
            //                     ], 200); 
            //                 }else {
            //                     DB::rollBack();
            //                     return response()->json([
            //                         'error'=>false,
            //                         'message'=> 'No Product created', 
            //                         'data'=>$products_created
            //                     ], 200); 
            //                 }

            //             }else {
            //                 return response()->json([
            //                     'error'=>false,
            //                     'message'=> "You're not authorized to create this ressource",
            //                 ], 400); 
            //             }
            //         }
            //     }
            //     else {
            //         return response()->json([
            //             'error'=>false,
            //             'message'=> "You're not authorized to create this ressource",
            //         ], 400); 
            //     }
            // }            
            return response()->json([
                'error'=>false,
                'message'=> "You're not authorized to create this ressource",
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
    public function procurementInventoryStore(Request $request)
    {
        try {
            
            DB::beginTransaction();

            $establishmnts = establishment::all();

            foreach ($establishmnts as $establishmnt) {

                $index = array_search($request->user()->id, json_decode($establishmnt->workers));

                if ($index !== false) {
                    
                    $userRoleTab = DB::table('users')
                        ->join('user_role_tabs', 'users.id', '=', 'user_role_tabs.user_id')
                        ->join('user_roles', 'user_roles.id', '=', 'user_role_tabs.user_role_id')
                        ->where('user_id',$request->user()->id)
                        ->get();

                    foreach($userRoleTab as $role){

                        if($role->nameRole == "manager" || "store-manager"){
                            
                            $inventoryStoreList = inventoryStore::where('establishment_id', $establishmnt->id)->get();
                            
                            $products_updated = [];

                            foreach ($request->drinkList as $drink) {
                                foreach ($inventoryStoreList as $inventoryStore) {

                                    if($drink['drink_id'] == $inventoryStore->drink_id){
                                        
                                        $inventoryStore->quantity += (integer) $drink['quantity'];
                                        $inventoryStore->price = (integer) $drink['price'] ?? $inventoryStore->price;
                                        $inventoryStore->save();

                                                        
                                        historicInventoryStore::create([                        
                                            'quantity' => (integer) $drink['quantity'],
                                            'price' => (double) $drink['price'] ?? $inventoryStore->price,
                                            'drink_id' => (integer) $inventoryStore->drink_id,
                                            'establishment_id' => $establishmnt->id,
                                            'type_operator' => 'input',
                                            'user_id' => $request->user()->id,
                                        ]); 

                                        array_push($products_updated, $inventoryStore);
                                    }                        
                                }
                            }              
                            DB::commit();
                            return response()->json([
                                'error'=>false,
                                'message'=> 'Products updated successfully in store', 
                                'data'=>$products_updated
                            ], 200);
                        }else {
                            return response()->json([
                                'error'=>false,
                                'message'=> "You're not authorized to create this ressource",
                            ], 400); 
                        }
                    }
                }else {
                    return response()->json([
                        'error'=>false,
                        'message'=> "You're not authorized to create this ressource",
                    ], 400); 
                }
            }            
            return response()->json([
                'error'=>false,
                'message'=> "You're not authorized to create this ressource",
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
    public function procurementWarehouse(Request $request)
    {
        try {
            
            DB::beginTransaction();

            $establishmnts = establishment::all();

            foreach ($establishmnts as $establishmnt) {

                $index = array_search($request->user()->id, json_decode($establishmnt->workers));

                if ($index !== false) {
                    
                    $userRoleTab = DB::table('users')
                        ->join('user_role_tabs', 'users.id', '=', 'user_role_tabs.user_id')
                        ->join('user_roles', 'user_roles.id', '=', 'user_role_tabs.user_role_id')
                        ->where('user_id',$request->user()->id)
                        ->get();

                    foreach($userRoleTab as $role){

                        if($role->nameRole == "manager" || "store-manager" || "admin"){
                            

                            $inventoryDrinkList = inventoryDrink::where('establishment_id', $establishmnt->id)->get();

                            $inventoryStoreList = inventoryStore::where('establishment_id', $establishmnt->id)
                                                    ->join('drinks', 'drinks.id', '=', 'inventory_stores.drink_id')
                                                    ->get();
                            $productsUpdated = [];
                            foreach ($request->drinkList as $drink) {
                                foreach ($inventoryStoreList as $inventoryStore) {

                                    if($drink['drink_id'] == $inventoryStore->drink_id){
                                        
                                        $isProductFound = false;
                                        foreach ($inventoryDrinkList as $inventoryDrink) {

                                            if( $drink['drink_id'] == $inventoryDrink->drink_id && ((integer) $inventoryStore->quantity - (integer) $drink['quantity'] >= 0)){


                                                $inventoryDrink->quantity += (integer) $drink['quantity'];
                                                $inventoryDrink->save();
                                                

                                                inventoryStore::where('id', $inventoryStore->id)
                                                    ->update([
                                                        "quantity" =>  (integer) $inventoryStore->quantity - (integer) $drink['quantity']
                                                ]);

                                                $isProductFound =true;
                                                                
                                                historicInventoryDrink::create([                        
                                                    'quantity' => (integer) $drink['quantity'],
                                                    'price' => (double) $inventoryDrink->price,
                                                    'drink_id' => (integer) $inventoryDrink->drink_id,
                                                    'establishment_id' => $establishmnt->id,
                                                    'type_operator' => 'input',
                                                    'user_id' => $request->user()->id,
                                                ]);  

                                                historicInventoryStore::create([                        
                                                    'quantity' => (integer) $drink['quantity'],
                                                    'price' => (double) $inventoryDrink->price,
                                                    'drink_id' => (integer) $inventoryDrink->drink_id,
                                                    'establishment_id' => $establishmnt->id,
                                                    'type_operator' => 'output',
                                                    'user_id' => $request->user()->id,
                                                ]);  

                                                array_push($productsUpdated, $inventoryDrink);
                                            }
                                        } 
                                        if($isProductFound == false  && ((integer) $inventoryStore->quantity - (integer) $drink['quantity'] >= 0)) {
                                            
                                            $product = inventoryDrink::create([
                                                'quantity' => (integer) $drink['quantity'],
                                                'price' => (double) $drink['price'],
                                                'drink_id' => (integer) $drink['drink_id'],
                                                'establishment_id' => $establishmnt->id,
                                            ]);
                                            
                                                                                        
                                            inventoryStore::where('id', $inventoryStore->id)
                                            ->update([
                                                "quantity" => (integer) $inventoryStore->quantity - (integer) $drink['quantity']
                                            ]);
                                                            
                                            historicInventoryDrink::create([                        
                                                'quantity' => (integer) $drink['quantity'],
                                                'price' => (double) $drink['price'],
                                                'drink_id' => (integer) $drink["drink_id"],
                                                'establishment_id' => $establishmnt->id,
                                                'type_operator' => 'input',
                                                'user_id' => $request->user()->id,
                                            ]); 

                                            historicInventoryStore::create([                        
                                                'quantity' => (integer) $drink['quantity'],
                                                'price' => (double) $inventoryStore->price,
                                                'drink_id' => (integer) $inventoryStore->drink_id,
                                                'establishment_id' => $establishmnt->id,
                                                'type_operator' => 'output',
                                                'user_id' => $request->user()->id,
                                            ]); 

                                            array_push($productsUpdated, $product);
                                        }
                                    }                   
                                }
                            }
                            if(count($productsUpdated) > 0){
                                DB::commit();
                                return response()->json([
                                    'error'=>false,
                                    'message'=> 'Products updated successfully', 
                                    'data'=>$productsUpdated
                                ], 200);
                            }else{
                                DB::commit();
                                return response()->json([
                                    'error'=>false,
                                    'message'=> 'No Products updated', 
                                    'data'=>$productsUpdated
                                ], 200);
                            }
                        }else {
                            return response()->json([
                                'error'=>false,
                                'message'=> "You're not authorized to create this ressource",
                            ], 400); 
                        }
                    }
                }else {
                    return response()->json([
                        'error'=>false,
                        'message'=> "You're not authorized to create this ressource",
                    ], 400); 
                }
            }            
            return response()->json([
                'error'=>false,
                'message'=> "You're not authorized to create this ressource",
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

            $userRoleTab = DB::table('users')
                ->join('user_role_tabs', 'users.id', '=', 'user_role_tabs.user_id')
                ->where('user_id',$request->user()->id)
                ->first();

            $userRole = userRole::where('id',$userRoleTab->user_role_id)
                ->first();

            if($establishmnt && $userRole->nameRole == "manager" || "barman"){
                $inventoryDrinkList = inventoryDrink::where('establishment_id', $establishmnt->id)->get();

                $productsUpdated = [];

                foreach ($request->drinkList as $drink) {
                    foreach ($inventoryDrinkList as $inventoryDrink) {
                        if($drink['drink_id'] == $inventoryDrink->drink_id){

                            $inventoryDrink->quantity += (integer) $drink['quantity'];
                            $inventoryDrink->save();

                            historicInventoryDrink::create([                        
                                'quantity' => (integer) (integer) $drink['quantity'],
                                'price' => (double) $inventoryDrink->price,
                                'drink_id' => (integer) $inventoryDrink->drink_id,
                                'establishment_id' => $establishmnt->id,
                                'type_operator' => 'input',
                                'user_id' => $request->user()->id,
                            ]);

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
                
                historicInventoryDrink::create([                        
                    'quantity' => (integer) $request->quantity,
                    'price' => (double) $request->price,
                    'drink_id' => (integer) $request->drink_id,
                    'establishment_id' => $establishmnt->id,
                    'type_operator' => 'input',
                    'user_id' => $request->user()->id,
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
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function productInStore(Request $request)
    {
        try {
            
                
            DB::beginTransaction();
            
            $establishmnt = establishment::where('user_id',$request->user()->id)->first();

            $userRoleTab = DB::table('users')
                ->join('user_role_tabs', 'users.id', '=', 'user_role_tabs.user_id')
                ->where('user_id',$request->user()->id)
                ->first();

            $userRole = userRole::where('id',$userRoleTab->user_role_id)
                ->first();

            if($establishmnt && $userRole->nameRole == "manager" || "barman"){
                
                $inventoryStoreList = inventoryStore::where('establishment_id', $establishmnt->id)->get();
                
                foreach ($inventoryStoreList as $inventoryStore) {
                    if($request->drink_id == $inventoryStore->drink_id){
                        return response()->json([
                            'error'=>false,
                            'message'=> 'This product has been created, please delete it or update that', 
                            'data'=>$inventoryStore
                        ], 400);
                    }                        
                }
                $product = inventoryDrink::create([
                    'quantity' => (integer) $request->quantity,
                    'price' => (double) $request->price,
                    'drink_id' => (integer) $request->drink_id,
                    'establishment_id' => $establishmnt->id,
                ]);  
                
                historicInventoryDrink::create([                        
                    'quantity' => (integer) $request->quantity,
                    'price' => (double) $request->price,
                    'drink_id' => (integer) $request->drink_id,
                    'establishment_id' => $establishmnt->id,
                    'type_operator' => 'input',
                    'user_id' => $request->user()->id,
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
