<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payment;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $payments=Payment::with('member')->whereStatus(1)->paginate(5);
        return view('home',compact('payments'));
    }
}
