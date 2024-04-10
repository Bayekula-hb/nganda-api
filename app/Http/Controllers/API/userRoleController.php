<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\userRole;
use Illuminate\Http\Request;
use Throwable;

class userRoleController extends Controller
{
    //Get all userRoles
    public function index() 
    {
        try {   
            $userRoles = userRole::orderBy('id', 'desc')->get();

            return response()->json([
                'error'=>false,
                'message'=> 'Data received successfully', 
                'data'=>$userRoles
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'error'=>true,
                'message' => 'Request failed, please try again',
                'data' => $e,
            ], 400);
        }
    }
}
