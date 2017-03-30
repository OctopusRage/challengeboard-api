<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Models\Challenge;
use App\User;
use App\Models\ChallengesTeacher;
use App\Models\ChallengesParticipant;
use App\Models\Trophy;
use Illuminate\Support\Facades\Hash;
use Validator;

class TrophyController extends Controller
{
  public function __construct() {
    $this->middleware('auth', ['only' => ['create', 'get_user_trophies']]);
  }

  public function create(Request $request){
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
          'messages'=> 'challenge not found'
        ]
      ]);
    }
    $teacher=$challenge->teachers->find($current_user->id);
    if (empty($teacher) && $current_user->isTeacher()) {
      return response()->json([
        'status' => 'fail',
        'errors' => [
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
        'errors' => [
          'messages' => 'Internal server errors'
        ]
      ], 500);
  }

  public function get_trophy_stats_by_id($id) {
    $trophy_gold_count = User::join('achievements', 'achievements.user_id', 'users.id')
      ->join('trophies', 'trophies.id', 'achievements.trophy_id')
      ->where('users.id', '=', $id)
      ->where('trophies.rank','=',1)
      ->count();
    $trophy_silver_count = User::join('achievements', 'achievements.user_id', 'users.id')
      ->join('trophies', 'trophies.id', 'achievements.trophy_id')
      ->where('users.id', '=', $id)
      ->where('trophies.rank','=',2)
      ->count();
    $trophy_bronze_count = User::join('achievements', 'achievements.user_id', 'users.id')
      ->join('trophies', 'trophies.id', 'achievements.trophy_id')
      ->where('users.id', '=', $id)
      ->where('trophies.rank','=',3)
      ->count();
    return response()->json([
      'status'=>'success',
      'data' => [
        'user' => User::find($id),
        'gold_count' => $trophy_gold_count,
        'silver_count' => $trophy_silver_count,
        'bronze_count' => $trophy_bronze_count,
      ]
    ], 200);
    
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

  public function get_user_trophies(){
    $current_user = Auth::user();
    if(!$current_user->isStudent()) {
      return response()->json([
        'status' => 'fail',
        'errors'=> [
          'messages' => 'Must be student'
        ]
      ], 401);
    }
    $trophies = Trophy::join('achievements', 'achievements.user_id', 'trophies.id')
      ->join('challenges', 'challenges.id', 'trophies.challenge_id')
      ->where('achievements.user_id', $current_user->id)
      ->select('trophies.*', 'tag', 'title')->get();
    return response()->json([
      'status' => 'success',
      'data' => [
        'trophies'=> $trophies
      ]
    ], 200);
  }

  public function get_user_trophies_by_student_id($id){
    $current_user = User::find($id);
    $trophies = Trophy::join('achievements', 'achievements.user_id', 'trophies.id')
      ->join('challenges', 'challenges.id', 'trophies.challenge_id')
      ->where('achievements.user_id', $current_user->id)
      ->select('trophies.*', 'tag', 'title')->get();
    return response()->json([
      'status' => 'success',
      'data' => [
        'trophies'=> $trophies
      ]
    ], 200);
  }

  public function get(){
    $current_user = Auth::user();
    $trophies = Trophy::select('trophies.*', 'challenges.title', 'challenges.tag')
    ->join('challenges', 'trophies.challenge_id', '=', 'challenges.id')->orderBy('trophies.created_at', 'desc')->get();
    
    return response()->json([
      'status' => 'success',
      'data' => [
        'trophies'=> $trophies
      ]
    ], 200);
  }
}