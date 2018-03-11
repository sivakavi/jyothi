<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check()) {
            if(Auth::user()->hasRole('dept'))
                return redirect('dept');
            else if(Auth::user()->hasRole('administrator') || Auth::user()->hasRole('admin'))
                return redirect('admin');
            else if(Auth::user()->hasRole('hr'))
                return redirect('hr');
        }
        return redirect('login');
    }
}
