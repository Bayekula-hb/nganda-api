<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\userRole;
use App\Models\userRoleTab;
use Illuminate\Http\Request;
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
}
