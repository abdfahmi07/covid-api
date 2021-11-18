<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(Request $request) {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

         $user = User::create([
             'name' => $fields['name'],
             'email' => $fields['email'],
             'password' => Hash::make($fields['password'])
         ]);

         $token = $user->createToken('user_token')->plainTextToken;
         
         $payloads = [
             "message" => 'User has been successfully registered',
             "success" => true,
             'user' => $user,
             'token' => $token
         ];

         return response()->json($payloads, 201);
    }    

    public function login(Request $request) {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check email
         $user = User::where('email', $fields['email'])->first();

        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Login failed',
                'success' => false
            ], 401);
        }

         $token = $user->createToken('user_token')->plainTextToken;
         
         $payloads = [
             'message' => 'Login successfull',
             'success' => true,
             'user' => $user,
             'token' => $token
         ];

         return response()->json($payloads);
    }    
    
    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        
        return response([
            "message" => 'Logged out',
            "success" => true
        ]);
    }

}