<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function Register(Request $request)
    {
        $user = User::create(["name" => $request->name, "email" => $request->email, "password" => Hash::make($request->password),]);
        return $user;
    }


    // public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');

    //     if (!Auth::attempt($credentials)) {
    //         return response()->json(['message' => 'Invalid credentials'], 401);
    //     }

    //     $user = Auth::user();
    //     $token = $user->createToken('API Token')->accessToken;

    //     return response()->json([
    //         'user' => $user,
    //         'token' => $token
    //     ]);
    // }

}
