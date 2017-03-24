<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Validator;

class UserController extends Controller
{
  const USER_TYPE_TEACHER = 1;
  const USER_TYPE_STUDENT = 2;

  public function create(Request $request) {
      $v = Validator::make($request->all(), [
          'name' => 'required|min:6|max:255',
          'password' => 'required',
          'password_confirmation' => 'same:password',
          'nim' => 'required|unique:users|min:6',
          'type' => 'required'
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
      $role = null;
      if ($request->input('type') == self::USER_TYPE_TEACHER) {
        $role = Role::where('role', '=', 'teacher')->first();
      } elseif($request->input('type') == self::USER_TYPE_STUDENT) {
        $role = Role::where('role', '=', 'student')->first();
      }

      if ($role == null ||$role->count()<1 ) {
        return response()->json([
          'status' => 'fail',
          'errors' => [
            'messages' => 'role does not exist'
          ]
        ], 422);  
      }

      $user->role_id = $role->id;
      
      if ($user->save()) {
        return response()->json([
          'status' => 'success',
          'data' => [
            'user' => $user
          ]
        ], 200);
      } else {
        return response()->json([
          'status' => 'fail',
          'errors' => [
            'messages' => 'Unauthorized'
          ]
        ], 401);
      }
  }
}
