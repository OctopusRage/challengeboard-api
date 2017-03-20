<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Models\Challenge;
use App\Models\ChallengesTeacher;
use App\Models\ChallengesParticipant;
use App\Models\Schedule;
use Illuminate\Support\Facades\Hash;
use Validator;

class ScheduleController extends Controller
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
        'event_time' => 'date_format:H:i|required',
        'event_date' => 'date|required',
        'challenge_id' => 'required',
        'places' => 'required',
        'is_online' => 'boolean',
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
    if (empty($teacher)) {
      return response()->json([
        'status' => 'fail',
        'data' => [
          'messages' => 'unauthorized access'
        ]
      ], 401);
    }

    $is_online = $request->input('is_online') ?: false;
    $schedule = new Schedule;
    $schedule->event_date = $request->input('event_date');
    $schedule->event_time = $request->input('event_time');
    $schedule->challenge_id = $request->input('challenge_id');
    $schedule->is_online = $is_online;
    $schedule->places = $request->input('places');
    if ($schedule->save()) {
      return response()->json([
        'status' => 'success',
        'data' => [
          'subject'=> $schedule
        ]
      ], 200);
    }
    return response()->json([
        'status' => 'fail',
        'errors' => $schedule->errors()
      ], 422);
  }

  public function get($id){
    $current_user = Auth::user();
    $schedule = Schedule::where('challenge_id', '=', $id)->get();
    $challenge = Challenge::find($id);
    if (empty($challenge)) {
      return response()->json([
        'status'=> 'fail',
        'errors'=> [
          'challenge'=> 'challenge not found'
        ]
      ]);
    }
    $participant=$challenge->participants->find($current_user->id);
    if (empty($participant) && $current_user->isStudent() ) {
      return response()->json([
        'status' => 'fail',
        'data' => [
          'messages' => 'unauthorized access'
        ]
      ], 401);
    }
    return response()->json([
      'status' => 'success',
      'data' => [
        'subjects'=> $schedule
      ]
    ], 200);
  }
}