<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Models\Challenge;
use App\Models\ChallengesTeacher;
use App\Models\ChallengesParticipant;
use Illuminate\Support\Facades\Hash;
use Validator;

class TeacherController extends Controller
{
  public function __construct() {
    $this->middleware('auth', ['only' => ['pending_request']]);
  }

  public function pending_request() {
    $challenge = Challenge::all();
    $current_user = Auth::user();
    if($current_user->isStudent()) {
      return response()->json([
        'status' => 'fail',
        'data' => [
          'messages' => 'unauthorized access'
        ]
      ], 401);
    }

    $pending_request = Challenge::join('challenges_participants', 'challenges_participants.challenge_id', '=', 'challenges.id')
      ->join('challenges_teachers', 'challenges_teachers.challenge_id', '=', 'challenges.id' )
      ->join('users', 'challenges_participants.user_id', '=', 'users.id')
      ->join('roles', 'users.role_id', '=', 'roles.id')
      ->where('roles.role', '=', 'student')
      ->where('challenges_teachers.user_id', '=', $current_user->id)
      ->where('challenges_participants.status', '=', false)
      ->select('challenges_participants.id', 'users.id as user_id', 'users.name', 'challenges_participants.status',
        'challenges.id as challenge_id', 'challenges.title'
      )
      ->get();
    return response()->json([
      'status' => 'success', 
      'data' => [
        'pending_request' => $pending_request
      ]
    ]);
  }

  public function pending_request_by_id($id) {
    $challenge = Challenge::find($id);
    $current_user = Auth::user();
    if($current_user->isStudent()) {
      return response()->json([
        'status' => 'fail',
        'data' => [
          'messages' => 'unauthorized access'
        ]
      ], 401);
    }
    
    $teacher = $challenge->teachers->find($current_user->id);
    if(empty($teacher)) {
      return response()->json([
        'status' => 'fail',
        'data' => [
          'messages' => 'unauthorized access'
        ]
      ], 401);
    }

    $pending_request = $challenge->participants->where('status', '=',0);
    return response()->json([
      'status' => 'success', 
      'data' => [
        'pending_request' => $pending_request
      ]
    ]);
  }
}