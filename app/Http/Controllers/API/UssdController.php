<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use App\Ussd_flow;
// use App\Member;
// use App\Payment;
// use App\Setting;
use Illuminate\Support\Facades\Validator;

class UssdController extends Controller
{
	public $newRequest0;
	public $msisdn0;
    public $input0;
    //public $sessionId;

	public function __construct()
	{
		$this->newRequest0=0;
		$this->msisdn0="";
		$this->input0="";
		//$this->sessionId=0;
	}

    public function index(Request $request)
    {
    	 $newRequest=$this->newRequest0 = $request->get('newRequest');
		$msisdn=$this->msisdn0 = $request->get('msisdn');
		$input=$this->input0 = $request->get('input');
        // $session = Ussd_flow::orderBy('session', 'desc')->limit(1)->value('session');
        // $session = $session + 1;
        // $tel = substr($msisdn, 2, 10);
  
if ($newRequest == "1") 
{
    // Ug_member::where(['telephone' => $tel, 'status' => 0])->delete();   

    $message = "Welcome Patriots";
    $data = array("action" => "FC", "message" => $message);
    // saveFlow('english', $message, $input, $session, $msisdn, 0, 0);

} 
else if ($newRequest == "0") 
{
}
return $data;
}
}
