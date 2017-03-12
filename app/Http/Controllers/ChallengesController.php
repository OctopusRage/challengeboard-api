<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Challenges;
use Illuminate\Support\Facades\Hash;
use Validator;

class ChallengesController extends Controller
{
  public function create(Request $request) {
      $user = Auth::user();
      if (!$user->isAdmin()){
        return response()->json([
          'status' => 'fail',
          'errors' => 'unauthorized access'
        ], 401);
      }
      
      $v = Validator::make($request->all(), [
          'title' => 'required|min:6|max:255',
          'event_date' => 'required|date',
          'prize' => 'numeric',
          'description' => 'required|min:10',
          'enroll_limit' => 'required|date',
          'status' => 'boolean',
      ]);
      
      if (!isset($request->status)) {
        $request->status = true;
      }

      if ($v->fails()){
        return response()->json([
          'status' => 'fail',
          'errors' => $v->errors()
        ], 422);
      }

      $challenges = new User;
      $challenges->title = $request-> input('title');
      $challenges->event_date = $request-> input('event_date');
      $challenges->prize = $request->input('prize');
      $challenges->description = $request->input('description');
      $challenges->enroll_limit = $request->input('enroll_limit');
      $challenges->status = $request->input('status');
      
      if ($challenges->save()) {
        return response()->json($challenges);
      } else {
        return response()->json(
          ['error' => 'Unauthorized'], 401);
      }
  }

}
