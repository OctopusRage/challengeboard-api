<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Models\Challenge;
use App\Models\Achievement;
use App\Models\Trophy;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Validator;

class AchievementController extends Controller
{
  public function __construct() {
    $this->middleware('auth', ['only' => ['create', 'join','mine']]);
  }
  
  public function index($id) {
    $achievements = Challenge::join('trophies','trophies.challenge_id','challenges.id')
      ->join('achievements', 'achievements.trophy_id', 'trophies.id')
      ->join('users', 'achievements.user_id', 'users.id')
      ->select('achievements.*', 'rank', 'challenges.title', 'users.name')
      ->where('users.id', $id)
      ->get();
      
    return response()->json([
      'status' => 'success',
      'user' => $achievements,
    ], 200);
  }

  public function create(Request $request) {
    $current_user = Auth::user();
    if ($current_user->isStudent()){
      return response()->json([
        'status' => 'fail',
        'errors' => [
          'messages' => 'unauthorized access'
        ]
      ], 401);
    }
    
    $v = Validator::make($request->all(), [
        'user_id' => 'integer|required',
        'trophy_id' => 'integer|required',
    ]);

    if ($v->fails()){
      return response()->json([
        'status' => 'fail',
        'errors' => $v->errors()
      ], 422);
    }
    $challenge = Trophy::join('achievements', 'achievements.trophy_id', 'trophies.id')
      ->where('user_id', $request->input('user_id'))->get();

    if($challenge->count() > 0){
      return response()->json([
        'status'=> 'fail',
        'errors'=> [
          'messages'=> 'aleady registered'
        ]
      ],422);
    }
    $achievement = new Achievement;
    $achievement->user_id = $request->input('user_id');
    $achievement->trophy_id = $request->input('trophy_id');
    if ($achievement->save()) {
      return response()->json([
        'status' => 'success',
        'data' => [
          'trophies'=> $achievement
        ]
      ], 200);
    }
    return response()->json([
        'status' => 'fail',
        'errors' => [
          'messages' => 'Internal server errors'
        ]
      ], 500);
  }
}
