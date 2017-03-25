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

      $user = User::where('nim', '=', $request->nim)->first();
      if(empty($user)) {
        return response()->json([
          'status' => 'fail',
          'errors' => [
            'messages' => 'user not found'
          ]
        ]);
      }
      if (!Hash::check($request->password, $user->encrypted_password)) {
        return response()->json([
          'status' => 'fail',
          'errors' => [
            'messages' => 'invalid password'
          ]
        ], 422);
      }
      $user = $user->join('roles','users.role_id', '=', 'roles.id')->select('users.id', 'nim', 'auth_token', 'name', 'roles.role')->first();
      return response()->json([ 
        'status' => 'success', 
        'data' => [
            'users' => $user
        ]
      ]);
    }
}
