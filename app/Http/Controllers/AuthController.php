<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(Request $request) {
        // Validate each field in request body
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        // Create new user
         $user = User::create([
             'name' => $fields['name'],
             'email' => $fields['email'],
             'password' => Hash::make($fields['password'])
         ]);
         
         $payloads = [
             "message" => 'User has been successfully registered',
             "success" => true,
             'user' => $user
         ];

         return response()->json($payloads, 201);
    }    

    public function login(Request $request) {
        // Validate each field in request body
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Get user's data by email 
         $user = User::where('email', $fields['email'])->first();

        //  Check if user not found and if password is wrong
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Login failed',
                'success' => false
            ], 401);
        }

        // Create token if user is available
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
        // Get a logged in user token and delete it
        $request->user()->currentAccessToken()->delete();
        
        return response([
            "message" => 'Logged out',
            "success" => true
        ]);
    }

}