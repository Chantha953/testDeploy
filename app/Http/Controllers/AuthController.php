<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function signup (Request $request){
        $request->validate([
            "name" => ["required","string","max:255"],
            "gender" => ["required","string","min:1","max:7"],
            "image" => ["required","file","mimetypes:image/png,image/jpeg","max:2048"],
            "phone" => ["required","degits_betweeen:8,10","unique:users,phone"],
            "email" => ["required","email","max:255"],
            "password" => ["required","string","min:4","max:255","confirmed"]
        ]);
        $user = User::create([
            "name" => $request->name,
            "gender" => $request->gender,
            "image" => $request->image,
            "phone" => $request->phone,
            "email" => $request->email,
            "password" => Hash::make($request->password)
        ]);
        $token = $user -> createToken("auth_token")->plainTextToken;
        return response()->json([
            "result" => true,
            "message" => "You has been signup seccessfully",
            "data" => $token
        ]);
    }
    public function login(Request $request){
        $request->validate([
            "email" => ["required","email","max:255"],
            "password" => ["required","string","min:4","max:255"],
        ]);
        $user = User::where("email",$request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                "reuslt" => false,
                "message" => "Login failed",
                "data" => []
            ]);
        }
        $token = $user -> createToken("auth_token")->plainTextToken;
        return response()->json([
            "result" => true,
            "message" => "You has been log in successfully",
            "token" => $token
        ]);
    }
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            "result" => true,
            "message" => "You has been log out seccessfully",
            "data" => []
        ]);
    }
}
