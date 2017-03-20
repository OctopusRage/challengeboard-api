<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Models\Challenge;
use App\Models\ChallengesTeacher;
use App\Models\ChallengesParticipant;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;
use Validator;

class SubjectController extends Controller
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
        'title' => 'required',
        'file' => 'file|required',
        'challenge_id' => 'required',
    ]);

    if ($v->fails()){
      return response()->json([
        'status' => 'fail',
        'errors' => $v->errors()
      ], 422);
    }
    $filename ="";
    if ($request->hasFile('file')){
      $destination = storage_path('uploaded_subjects');
      $file = $request->file('file');
      $file_extension = $file->extension();
      $filename = str_random('12').".".$file_extension;
      $file->move($destination, $filename);
    }
    
    $subject = new Subject;
    $subject->title = $request->input('title');
    $subject->review = $request->input('review');
    $subject->challenge_id = $request->input('challenge_id');
    $subject->file = $filename;
    if ($subject->save()) {
      return response()->json([
        'status' => 'success',
        'data' => [
          'subject'=> $subject
        ]
      ], 200);
    }
    return response()->json([
        'status' => 'fail',
        'errors' => $subject->errors()
      ], 422);
  }
  public function get($id){
    $current_user = Auth::user();
    $subject = Subject::where('challenge_id', '=', $id)->get();   
    return response()->json([
      'status' => 'success',
      'data' => [
        'subjects'=> $subject
      ]
    ], 200);
  }
}