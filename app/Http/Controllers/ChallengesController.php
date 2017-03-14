<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Models\Challenge;
use Illuminate\Support\Facades\Hash;
use Validator;

class ChallengesController extends Controller
{
  public function __construct() {
    $this->middleware('auth');
  }

  public function create(Request $request) {
      $user = Auth::user();
      if ($user->isStudent()){
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
      $status = true;
      if ($request->input('status')) {
        $status = $request->input('status');
      }

      if ($v->fails()){
        return response()->json([
          'status' => 'fail',
          'errors' => $v->errors()
        ], 422);
      }

      $challenges = new Challenge;
      $challenges->title = $request->input('title');
      $challenges->event_date = $request->input('event_date');
      $challenges->prize = $request->input('prize');
      $challenges->description = $request->input('description');
      $challenges->enroll_limit_date = $request->input('enroll_limit');
      $challenges->status = $status;
      
      if ($challenges->save()) {
        return response()->json($challenges);
      } else {
        return response()->json(
          ['error' => 'Unauthorized'], 401);
      }
  }

}
