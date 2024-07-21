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
            // $userRoles = userRole::orderBy('id', 'desc')->get();
            $userRoles = userRole::orderBy('id', 'desc')->cursor()
                            ->filter(function ($userRole){
                                return $userRole->nameRole != "admin" && $userRole->nameRole != "manager";
                            });
            // Supprimer les rôles "admin" et "manager"
            // $filteredRoles = $userRoles->filter(function ($role) {
            //     return !in_array($role->nameRole, ['admin', 'manager']);
            // });

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
