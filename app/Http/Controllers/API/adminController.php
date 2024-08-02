<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\establishment;
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
