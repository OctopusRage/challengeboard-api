<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Models\Challenge;
use App\Models\ChallengesTeacher;
use App\Models\ChallengesParticipant;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\User;

class StudentController extends Controller
{
  public function __construct() {
    $this->middleware('auth');
  }
  public function challenges() {
    $current_user = Auth::user();
    if (!$current_user->isStudent()) {
      return response()->json([
        'status' => 'fail',
        'data' => [
          'messages' => 'unauthorized access'
        ]
      ], 401);
    }
    $challenges = User::join('challenges_participants', 'challenges_participants.user_id','=', 'users.id')
      ->join('challenges', 'challenges_participants.challenge_id', '=', 'challenges.id')
      ->where('challenges_participants.status','=', 1)
      ->where('users.id','=', $current_user->id)
      ->select('challenges.*')
      ->get();
    
    return response()->json([
      'status' => 'success', 
      'data' => [
        'challenges' => $challenges
      ]
    ]);
  }
}