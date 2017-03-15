<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Models\Challenge;
use App\Models\ChallengesParticipant;
use Illuminate\Support\Facades\Hash;
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
        return response()->json([
          'status' => 'success',
          'user' => $challenges,
        ], 200);
      } else {
        return response()->json(
          ['error' => 'Unauthorized'], 401);
      }
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
      $challenges = Challenge::paginate($limit);
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
            'user_type' => 'must be student'
          ]
        ], 401);
      }

      $challenges = Challenge::find($id);
      if (empty($challenges)) {
        return response()->json([
          'status' => 'fail',
          'errors' => [
            'challenges' => 'not found'
          ]
        ], 422);
      }

      $isRegistered = ChallengesParticipant::whereRaw("challenges_id = ".$id." AND users_id = ".$user->id)->first();
      if (!empty($isRegistered)) {
        return response()->json([
          'status' => 'fail',
          'errors' => [
            'join_challenge' => 'participant already registered',
            'status' => ($isRegistered->statuts==1 ? "accepted":"waiting confirmation")
          ]
        ], 422);
      }

      $join_challenge = new ChallengesParticipant;
      $join_challenge->users_id = $user->id;
      $join_challenge->challenges_id = $id;

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
