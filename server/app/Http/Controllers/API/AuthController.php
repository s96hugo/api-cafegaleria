<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;
use Dotenv\Result\Success;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request){

        $creds = $request->only(['email', 'password']);
        
        if(!$token=auth()->attempt($creds)){
            return response()->json([
                'success' => false
                
            ]);
        }

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => Auth::user()
        ]);
    }

    public function register(Request $request){

        $encryptedPass = Hash::make($request->password);
        $user = new User();

        try{
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = $encryptedPass;
            
            $user->save();
            
            return response()->json([
                'success' => true
            ]);
            
        }
        catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e
            ]);
        }
    }

    public function logout(Request $req){
        try{
            JWTAuth::invalidate(JWTAuth::parseToken($req->token));
            return response()->json([
                'success' => true
            ]);


        }
        catch(Exception $e){
            return response()->json([
                'success' => false,
                $e
            ]);

        }
    }

    public function getUsers(){
        $users = User::all();
        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }

    public function editUser($id, Request $req){
        $user = User::findOrFail($id);
        $encryptedPass = Hash::make($req->password);
        $user->email = $req->email;
        $user->password = $encryptedPass;
        $user->name = $req->name;
        $user->update();
        return response()->json([
            'success' => true
        ]);
    }

    public function getCurrent($id){
        $user = User::findOrFail($id);
        return response()->json([
            'success' => true,
        ]);
    }

}
