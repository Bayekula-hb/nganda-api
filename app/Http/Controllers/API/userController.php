<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\userRole;
use App\Models\establishment;
use App\Models\userRoleTab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

    
    public function auth(Request $request)
    {
        try {
            $userFind = User::where('email', $request->userName)->orWhere('phoneNumber', $request->userName)->first();
            
            if( $userFind){
                if (Hash::check($request->password, $userFind->password)) {

                    $userRoles = userRoleTab::where('user_id', $userFind->id)->get();
                    $userRoleTab = [];
                    foreach($userRoles as $role){
                        $userRole = userRole::where('id', $role->user_role_id)->first();
                        if($userRole){
                            $userRoleObject = (object) [
                                'nameRole' => $userRole->nameRole,
                                'id' => $userRole->id,
                            ];
                            array_push($userRoleTab, $userRoleObject );
                        }
                    }

                    $token = $userFind->createToken($userFind->id);

                    return response()->json([
                        'error'=>false,
                        'message'=> 'User is logging successful', 
                        'data'=>[
                           'lastName' => $userFind->lastName,
                           'middleName' => $userFind->middleName,
                           'firstName' => $userFind->firstName,
                           'gender' => $userFind->gender,
                           'email' => $userFind->email,
                           'phoneNumber' => $userFind->gender,
                           'id' => $userFind->id,
                           'token' => $token->plainTextToken,
                           'userRoles' => $userRoleTab,
                        ], 
                    ], 200);
                } else {
                    return response()->json([
                        'error'=>true,
                        'message'=> 'The password is incorrect', 
                    ], 400);
                }
            }else{
                return response()->json([
                    'error'=>true,
                    'message'=> 'This user is not found : '.$request->username, 
                ], 400);

            }
        } catch (Throwable $e) {

            return response()->json([
                'error'=>true,
                'message'=> 'Something went wrong, please try again',
            ], 400);
        }
    }

    public function receiver(Request $request)
    {
        
        try {
                $establishmnt = establishment::where('user_id',$request->user()->id)
                            ->join('users', 'establishments.user_id', '=', 'users.id')                           
                            ->first();

                $userRoleTab = DB::table('users')
                            ->join('user_role_tabs', 'users.id', '=', 'user_role_tabs.user_id')
                            ->where('user_id',$request->user()->id)
                            ->first();

                $userRole = userRole::where('id',$userRoleTab->user_role_id)
                            ->first();

                if($establishmnt && $userRole->nameRole == "manager"){

                    $userRoleReceiver = userRole::where('nameRole', 'receiver')
                                                    ->first();
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
                        'user_role_id' => $userRoleReceiver->id,
                    ]);

                    DB::commit();

                    return response()->json([
                        'error'=>false,
                        'message'=> 'User receiver created with successfully', 
                        'data' => [
                            "user" => $user,
                            "userRoles" => $userRoleTab,
                        ]
                    ], 200); 
                }else{
                    return response()->json([
                        'error'=>true,
                        'message' => 'You are authorized to create the receiver',
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

    public function cashier(Request $request)
    {
        
        try {
                $establishmnt = establishment::where('user_id',$request->user()->id)
                            ->join('users', 'establishments.user_id', '=', 'users.id')                           
                            ->first();

                $userRoleTab = DB::table('users')
                            ->join('user_role_tabs', 'users.id', '=', 'user_role_tabs.user_id')
                            ->where('user_id',$request->user()->id)
                            ->first();

                $userRole = userRole::where('id',$userRoleTab->user_role_id)
                            ->first();

                if($establishmnt && $userRole->nameRole == "manager"){

                    $userRoleCashier = userRole::where('nameRole', 'cashier')
                                                    ->first();

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
                        'user_role_id' => $userRoleCashier->id,
                    ]);

                    DB::commit();

                    return response()->json([
                        'error'=>false,
                        'message'=> 'User cashier created with successfully', 
                        'data' => [
                            "user" => $user,
                            "userRoles" => $userRoleTab,
                        ]
                    ], 200); 
                }else{
                    return response()->json([
                        'error'=>true,
                        'message' => 'You are authorized to create the receiver',
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

    public function banner(Request $request)
    {
        
        try {
                $establishmnt = establishment::where('user_id',$request->user()->id)
                            ->join('users', 'establishments.user_id', '=', 'users.id')                           
                            ->first();

                $userRoleTab = DB::table('users')
                            ->join('user_role_tabs', 'users.id', '=', 'user_role_tabs.user_id')
                            ->where('user_id',$request->user()->id)
                            ->first();

                $userRole = userRole::where('id',$userRoleTab->user_role_id)
                            ->first();

                if($establishmnt && $userRole->nameRole == "manager"){

                    $userRoleCashier = userRole::where('nameRole', 'banner')
                                                    ->first();

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
                        'user_role_id' => $userRoleCashier->id,
                    ]);

                    DB::commit();

                    return response()->json([
                        'error'=>false,
                        'message'=> 'User banner created with successfully', 
                        'data' => [
                            "user" => $user,
                            "userRoles" => $userRoleTab,
                        ]
                    ], 200); 
                }else{
                    return response()->json([
                        'error'=>true,
                        'message' => 'You are authorized to create the receiver',
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

    public function updatePassword (Request $request)
    {
        try {
        
            $user = auth()->user();
            $user->password = Hash::make($request->input('password'));
            $user->save();
            
            return response()->json([
                'error'=>false,
                'message'=> 'Password updated successfully', 
                'data' => $user,
            ], 200); 
        }catch (\Throwable $th) {
            return response()->json([
                'error'=>true,
                'message' => 'Request failed, please try again',
                'data' => $th,
            ], 400);     
        }
    }

}
