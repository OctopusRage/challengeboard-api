<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Models\Challenge;
use App\Models\ChallengesParticipant;
use App\Models\ChallengesTeacher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Validator;

class ChallengesController extends Controller
{
  public function __construct() {
    $this->middleware('auth', ['only' => ['create', 'join']]);
  }

  public function create(Request $request) {
      $user = Auth::user();
      if ($user->isStudent()){
        return response()->json([
          'status' => 'fail',
          'errors' => [
            'messages'=> 'unauthorized access'
          ]
        ], 401);
      }
      
      $v = Validator::make($request->all(), [
          'title' => 'required|min:6|max:255',
          'event_date' => 'required|date',
          'prize' => 'numeric',
          'description' => 'required|min:10',
          'enroll_limit' => 'required|date',
          'tag' => 'required',
          'status' => 'boolean',
          'picture' => 'file',
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
      $filename ="";
      $checkRoom = Challenge::where('room_id', $request->input('room_id'))->first();
      if (!empty($checkRoom)) {
        return response()->json([
          'status' => 'fail',
          'errors' => [
            'messages' => 'room_id has been taken'
          ]
        ], 422);
      }
      if ($request->hasFile('picture')){
        $destination = storage_path('uploaded_subjects');
        $file = $request->file('picture');
        $file_extension = $file->extension();
        $filename = str_random('12').".".$file_extension;
        $file->move($destination, $filename);
      }

      $challenges = new Challenge;
      $challenges->title = $request->input('title');
      $challenges->event_date = $request->input('event_date');
      $challenges->prize = $request->input('prize');
      $challenges->description = $request->input('description');
      $challenges->enroll_limit_date = $request->input('enroll_limit');
      $challenges->tag = $request->input('tag');
      $challenges->room_id = $request->input('room_id');
      if ($request->hasFile('picture')){
        $challenges->picture = $filename;
      }
      $challenges->status = $status;
      
      if ($challenges->save()) {
        $challenges_teacher = new ChallengesTeacher;
        $challenges_teacher->user_id = $user->id;
        $challenges_teacher->challenge_id = $challenges->id;
        $challenges_teacher->save();
        return response()->json([
          'status' => 'success',
          'user' => $challenges,
        ], 200);
      } else {
        return response()->json([
          'status' => 'fail',
          'error' => [
              'messages'=>  'unauthorized'
            ]
          ], 401);
      }
  }

  public function mine(Request $request) {
      $user = Auth::user();
      $v = Validator::make($request->all(), [
          'limit' => 'integer',
      ]);

      if ($v->fails()){
        return response()->json([
          'status' => 'fail',
          'errors' => $v->errors()
        ], 422);
      }
      $limit = $request->input('limit') ?: 20 ;
      $challenges = Challenge::join('challenges_teachers', 'challenges.id', '=', 'challenges_teachers.challenge_id')
        ->groupBy('challenges.id')
        ->select('challenges.*')
        ->paginate($limit);

      return response()->json([
        'status' => 'success',
        'data' => [
          'challenges' => $challenges->toArray()['data'],
          'total' => $challenges->count(),
          'current_page'=> $challenges->currentPage(),
        ]
      ], 200);
  }

  public function index(Request $request) {
      $user = Auth::user();
      $v = Validator::make($request->all(), [
          'limit' => 'integer',
      ]);

      if ($v->fails()){
        return response()->json([
          'status' => 'fail',
          'errors' => $v->errors()
        ], 422);
      }
      $limit = $request->input('limit') ?: 20 ;
      $challenges = Challenge::select(DB::raw('challenges.*, count(challenges_participants.id) as participant_count'))
        ->leftJoin('challenges_participants', 'challenges.id', '=', 'challenges_participants.challenge_id')
        ->groupBy('challenges.id')
        ->paginate($limit);

      return response()->json([
        'status' => 'success',
        'data' => [
          'challenges' => $challenges->toArray()['data'],
          'total' => $challenges->count(),
          'current_page'=> $challenges->currentPage(),
        ]
      ], 200);
  }

  

  public function join($id) {
      $user = Auth::user();
      if (!$user->isStudent()){
        return response()->json([
          'status' => 'fail',
          'errors' => [
            'messages' => ' user type must be student'
          ]
        ], 401);
      }

      $challenges = Challenge::find($id);
      if (empty($challenges)) {
        return response()->json([
          'status' => 'fail',
          'errors' => [
            'messages' => 'challenges not found'
          ]
        ], 422);
      }

      $isRegistered = ChallengesParticipant::whereRaw("challenge_id = ".$id." AND user_id = ".$user->id)->first();
      if (!empty($isRegistered)) {
        return response()->json([
          'status' => 'fail',
          'errors' => [
            'messages' => 'participant already registered',
            'status' => ($isRegistered->statuts==1 ? "accepted":"waiting confirmation")
          ]
        ], 422);
      }

      $join_challenge = new ChallengesParticipant;
      $join_challenge->user_id = $user->id;
      $join_challenge->challenge_id = $id;

      if ($join_challenge->save()) {
        return response()->json([
          'status' => 'success',
          'data' => [
            'challenges_participants' => $join_challenge,
          ]
        ], 201);
      } else {
        return response()->json([
          'status' => 'fail',
          'errors' => [
            'messages' => 'fail to save data'
          ]
        ], 500);
      }
      
  }

}
