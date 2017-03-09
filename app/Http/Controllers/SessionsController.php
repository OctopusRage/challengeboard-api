<?php

namespace App\Http\Controllers;

class SessionsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        $this->middleware('auth');
    }

    public function create() {
        return "aku ganteng0";
    }

    //
}
