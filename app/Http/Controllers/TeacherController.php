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
    $challenge = Challenge::all($id);
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

    $list_of_pending = ChallengesParticipant::where('user_id')
    $pending_request = $challenge->participants->where('status', '=',0);
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