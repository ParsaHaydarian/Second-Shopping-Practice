<?php

namespace App\Http\Controllers;

use App\Models\BaseProduct;
use App\Models\SizeColor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);
        $user = User::query()->create($validatedData);
        $token = $user->createToken('user_token', ['user'])->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::query()->where('email', $validatedData['email'])->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(["message" => "Invalid Credentials"]);
        }
        $token = $user->createToken('user_token', ['user'])->plainTextToken;
        return response()->json(["user" => $user, "token" => $token]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(["message" => "Logged out"]);
    }
    public function viewShop(Request $request){
        return BaseProduct::query()->with('sizeColors')->get();
    }
}


