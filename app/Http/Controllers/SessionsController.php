<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Validator;
use Storage;
class SessionsController extends Controller
{
    public function create(Request $request) {
        $v = Validator::make($request->all(), [
          'nim' => 'required',
          'password' => 'required',
      ]);

      if ($v->fails()){
        return response()->json([
          'status' => 'fail',
          'errors' => $v->errors()
        ], 422);
      }

      $user = User::where('email', '=', $request->nim)->first();
      if(empty($user)) {
        return response()->json([
          'status' => 'fail',
          'errors' => [
            'messages' => 'user not found'
          ]
        ],422);
      }
      if (!Hash::check($request->password, $user->encrypted_password)) {
        return response()->json([
          'status' => 'fail',
          'errors' => [
            'messages' => 'invalid password'
          ]
        ], 422);
      }
      $user = User::select('users.id', 'nim', 'auth_token', 'name', 'email','roles.role')
      ->join('roles', 'users.role_id', '=', 'roles.id')
      ->where('users.email', '=', $request->input('nim'))
      ->first();
      
      return response()->json([ 
        'status' => 'success', 
        'data' => [
            'users' => $user
        ]
      ]);
    }
}
