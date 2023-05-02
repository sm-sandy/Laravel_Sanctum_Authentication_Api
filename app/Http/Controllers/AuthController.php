<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
   public function register(Request $request)
   {

    //  return $request->all();

      $fields = $request->validate([
           'name' => 'required|string',
           'email' => 'required|string|unique:users,email',
           'password'=>'required|string|confirmed',
           'user_type' => 'required|string'
      ]);

      $user = User::create([
        'name'=>$fields['name'],
        'email'=>$fields['email'],
        'password'=>bcrypt($fields['password']),
        'user_type'=>$fields['user_type']
      ]);

      $token =$user->createToken('appToken')->plainTextToken;

      return  response()->json([
        'user' => $user,
        'token'=>$token
      ],201);
   }

   public function login(Request $request)
   {
      $fields = $request->validate([
           'email' => 'required|string|email',
           'password'=>'required|string'
      ]);

      $user = User::where('email',$fields['email'])->first();

      if (!$user || !Hash::check($fields['password'],$user->password)) {
        return response([
            'message' => 'Bad Creditials'
        ],401);
      }

      $token =$user->createToken('appToken')->plainTextToken;

      return  response()->json([
        'user' => $user,
        'token'=>$token
      ],201);
   }

   public function logout()
   {
      auth()->user()->tokens()->delete();
      return response([
        'message'=>'Logged out'
      ]);
   }
}
