<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Models\Challenge;
use App\Models\ChallengesTeacher;
use App\Models\ChallengesParticipant;
use App\Models\Trophy;
use Illuminate\Support\Facades\Hash;
use Validator;

class TrophyController extends Controller
{
  public function __construct() {
    $this->middleware('auth', ['only' => ['create']]);
  }

  public function create(Request $request){
    $current_user = Auth::user();
    if ($current_user->isStudent()){
      return response()->json([
        'status' => 'fail',
        'errors' => 'unauthorized access'
      ], 401);
    }
    
    $v = Validator::make($request->all(), [
        'rank' => 'integer|required',
        'challenge_id' => 'integer|required',
    ]);

    if ($v->fails()){
      return response()->json([
        'status' => 'fail',
        'errors' => $v->errors()
      ], 422);
    }
    $challenge = Challenge::find($request->input('challenge_id'));
    if (empty($challenge)) {
      return response()->json([
        'status'=> 'fail',
        'errors'=> [
          'challenge'=> 'challenge not found'
        ]
      ]);
    }
    $teacher=$challenge->teachers->find($current_user->id);
    if (empty($teacher) && $current_user->isTeacher()) {
      return response()->json([
        'status' => 'fail',
        'data' => [
          'messages' => 'unauthorized access'
        ]
      ], 401);
    }

    $exist_trophies = Trophy::where('challenge_id', $request->input('challenge_id'))
      ->where('rank', $request->input('rank'))->count();
    if($exist_trophies > 0) {
      return response()->json([
        'status' => 'fail',
        'errors' => [
          'trophy' => 'rank already exist'
        ]
      ], 422);
    }

    $trophy = new Trophy;
    $trophy->challenge_id = $request->input('challenge_id');
    $trophy->rank = $request->input('rank');
    if ($trophy->save()) {
      return response()->json([
        'status' => 'success',
        'data' => [
          'trophies'=> $trophy
        ]
      ], 200);
    }
    return response()->json([
        'status' => 'fail',
        'errors' => $trophy->errors()
      ], 422);
  }

  public function get_by_id($id){
    $current_user = Auth::user();
    $challenge = Challenge::find($id);
    if (empty($challenge)) {
      return response()->json([
        'status'=> 'fail',
        'errors'=> [
          'challenge'=> 'challenge not found'
        ]
      ]);
    }
    $trophy = Trophy::where('challenge_id', '=', $id)->get();    
    return response()->json([
      'status' => 'success',
      'data' => [
        'trophies'=> $trophy
      ]
    ], 200);
  }

  public function get(){
    $current_user = Auth::user();
    $trophy = Trophy::all();
    
    return response()->json([
      'status' => 'success',
      'data' => [
        'trophies'=> $trophy
      ]
    ], 200);
  }
}