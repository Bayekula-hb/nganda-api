<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\establishment;
use App\Models\User;
use App\Models\userRoleTab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class signController extends Controller
{
    //
        /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function singup(Request $request)
    {
        try {
            // $requestUser = $request;

            // DB::transaction(function () {

                // return response()->json([
                //     'error'=>false,
                //     'message' => 'Request failed, please try again',
                //     'data' => $requestUser,
                // ], 400);   
                DB::beginTransaction();

                $user = User::create([
                    'firstName' => $request->firstName,
                    'middleName' => $request->middleName,
                    'lastName' => $request->lastName,
                    'email' => $request->email,
                    'phoneNumber' => $request->phoneNumber,
                    'password' => Hash::make($request->password),
                    'gender' => $request->gender,
                ]);

                $userRoleTab = userRoleTab::create([
                    'user_id' => $user->id,
                    'user_role_id' => 2,
                ]);

                
                $establishment = establishment::create([
                    'nameEtablishment' => $request->nameEtablishment,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'address' => $request->address,
                    'pos' => $request->pos,
                    'numberPos' => $request->numberPos,
                    'user_id' => $user->id,
                ]);

                DB::commit();
                // On retourne les informations du nouvel utilisateur en JSON
                return response()->json([
                    'error'=>false,
                    'message'=> 'User created & establishment are created with successfully', 
                    'data' => [
                        "user" => $user,
                        "establishment" => $establishment
                    ]
                ], 200); 

            // });
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
