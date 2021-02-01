<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Ussd_flow;
use App\Member;
use App\Payment;
use App\Setting;
use App\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;


class UssdController extends Controller
{
	public $newRequest0;
	public $msisdn0;
    public $input0;
    public $sessionId;

	public function __construct()
	{
		$this->newRequest0=0;
		$this->msisdn0="";
		$this->input0="";
		$this->sessionId=0;
	}
 public function index(Request $request)
{
    $newRequest=$this->newRequest0 = $request->get('newRequest');
    $msisdn=$this->msisdn0 = $request->get('msisdn');
    $input=$this->input0 = $request->get('input');
    $sessionId=$this->input0 = $request->get('sessionId');
  
    $tel = substr($msisdn, 2, 10);
    $session = Ussd_flow::orderBy('session', 'desc')->limit(1)->value('session');
    $session = $session + 1;	

  
if ($newRequest == "1") 
{
    Member::where(['telephone' => $msisdn, 'status' => 0])->forceDelete();

	//$message = "Welcome to Patriots/Ikaze kuri Patriots/#1.English#2.Kinyarwanda";    
    $message = "Welcome to Patriots";    
    $data = array("action" => "FC", "message" => $message);
    saveFlow('english', $message, $input, $session, $msisdn, 0, 0);

} 
else if ($newRequest == "0") 
{
	$query = Ussd_flow::where('telephone', $msisdn)->orderBy('id', 'desc')->limit(1)->first();
    $session = $query->session;
    $level = $query->level;
    $sublevel1 = $query->sublevel1;
    $language = $query->language;

    $b = Dict($language, 'back');
    if ($input == "0") {
        Ussd_flow::where('session', $session)->orderBy('id', 'desc')->limit(1)->delete();
        $message = Ussd_flow::where('session', $session)->orderBy('id', 'desc')->limit(1)->value('message');
        $data = FC($message);
    } else if ($input != "*") {
        if ($level == "0") {
            if ($input == "1")
                $language = 'english';
            else if ($input == "2")
                $language = 'kinyarwanda';            
            else $data = InvalidInput($language);
            updateMembre('telephone',$msisdn,'language', $language);

            $message = Dict($language, 'selectAction');
            $data = FC($message);
            saveFlow($language, $message, $input, $session, $msisdn, "1", "1");
        }
		else if ($level == "1") {
            if ($input == "1") {
                $message = Dict($language, 'membership');
                $data = array(
                    "action"  => "FC",
                    "message" => $message);
                saveFlow($language, $message, $input, $session, $msisdn,'membership', 1);
            } //payment
            else if ($input == "2") {
                //$message = Dict($language, 'urCode');
                //$message = Dict($language, 'whoPay').Dict($language, 'back');
                $data = UnderMaintenance();
                //saveFlow($language, $message, $input, $session, $msisdn, "t0.1", "t1");
            }
            else if ($input == "3") {
                $data = UnderMaintenance();
            } else $data = InvalidInput($language);
        }
        else if ($level == "membership") {
            $response =Http::get('https://mo.mopay.rw/api/v1/person?msisdn='.$msisdn);
            $first_name = json_decode($response->getBody())->firstName;
            $last_name = json_decode($response->getBody())->lastName;
            $member=Member::create(['first_name'=>$first_name,'last_name'=>$last_name,'telephone'=>$msisdn,'language'=>$language,'session'=>$session]);

            if ($input == "1") {
                $message = Dict($language, 'memberCategory');
                $data =FC($message);
                saveFlow($language, $message, $input, $session, $msisdn,'memberCategory', 1);
            } //payment
            else if ($input == "2") {
            $member->update(['category_id'=>5]);
            $message = Dict($language, 'urDetails').'#'.$member->full_name.'#Tel: '.$tel.Dict($language, 'membershipName').$member->category->name.Dict($language, 'yesNo');
            $data =FC($message);
            saveFlow($language, $message, $input, $session, $msisdn,'confirmMember', 1);
}
else $data = InvalidInput($language);
}
else if ($level == "memberCategory") {
    memberUpdate('category_id',$input,$session);
    $member=Member::where('telephone',$msisdn)->first();
    $message = Dict($language, 'urDetails').'#'.$member->full_name.'#Tel: '.$tel.Dict($language, 'membershipName').$member->category->name.Dict($language, 'yesNo');
    $data =FC($message);
    saveFlow($language, $message, $input, $session, $msisdn,'confirmMember', 1);
} 
if($level=='confirmMember'){
     if ($input == "1") {
                memberUpdate('status', 1,$session);
                $member = Member::where('telephone',$msisdn)->orderBy('id', 'desc')->limit(1)->first();
                if($member->category_id!=5){
                 Payment::create(['telephone'=>$msisdn,'amount'=>$member->category->amount,'fee_type'=>$member->category->name,'fee_name'=>$member->category->name]);   
                $message = Dict($language, 'pin');
                $response = Pay($member->category->amount, $msisdn);
                $data = array("action" => "FB", "message" => $message);
                }
                else{
                $message=$member->last_name.' , '. Dict($language, 'successfullyRegistered');
                //sendSMS($message0, $telephone,'registration'.$member->id);
             
                $data = array("action" => "FB", "message" => $message);
            }
            } else if ($input == "2") {
                $message = Dict($language, 'yesCancel');
                $data = FC($message);
                saveFlow($language, $message, $input, $session, $msisdn, 8, 1);
            } else $data = InvalidInput($language);
}
           else if ($level == "8") {
            if ($input == 1) {
                $message = Dict($language, 'selectAction');
                $data = FC($message);
                saveFlow($language, $message, $input, $session, $msisdn, 1, 1);
            } else if ($input == 2) {
                $message = Dict($language, 'anyTime');
                $data = array("action" => "FB", "message" => $message);
            }
											}
}
}
return $data;
}
}
