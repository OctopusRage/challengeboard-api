<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Validator;

class UserController extends Controller
{
  public function create(Request $request) {
      $v = Validator::make($request->all(), [
          'name' => 'required|min:6|max:255',
          'password' => 'required',
          'password_confirmation' => 'same:password',
          'nim' => 'required|unique:users|min:6'
      ]);

      if ($v->fails()){
        return response()->json([
          'status' => 'fail',
          'errors' => $v->errors()
        ], 422);
      }

      $user = new User;
      $user->nim = $request-> input('nim');
      $user->name = $request->input('name');
      $user->encrypted_password = Hash::make($request->input('password'));
      $user->auth_token = str_random(20);
      
      if ($user->save()) {
        return response()->json($user);
      } else {
        return response()->json(
          ['error' => 'Unauthorized'], 401);
      }
  }

}
