<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use PHPUnit\Event\Code\Throwable;

class userController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {   
            $users = User::orderBy('id', 'desc')->get();

            return response()->json([
                'error'=>false,
                'message'=> 'Data received successfully', 
                'data'=>$users
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
