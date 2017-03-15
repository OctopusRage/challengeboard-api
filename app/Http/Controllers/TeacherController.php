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
    $this->middleware('auth', ['only' => ['create', 'join']]);
  }

  public function pending_request($id) {
    $challenge = Challenge::find($id);
    return response()->json([
      'data' => $challenge->participants
    ]);
  }
}