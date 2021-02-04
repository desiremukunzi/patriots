<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Payment;
use App\Ussd_flow;
use App\Sms_outbox;
use Log;

class PaymentsController extends Controller
{

 public function momo(Request $request) {
        $external_transaction_id = $request->get('external_transaction_id');
        $momo_ref_number = $request->get('momo_ref_number');
        $status_code = $request->get('status_code');
             
if($status_code==200){
try {
$record=Payment::with('member.category')->where(['trxid'=>$external_transaction_id])->first();
$payment_id=$record->id;
$fee_type=$record->fee_type;
$tel=$record->tel;
$session=$record->session;
$language = Ussd_flow::where('telephone', $tel)->orderBy('id', 'desc')
->limit(1)->value('language');
//$language="english";

$fee=$record->fee_type;
$amount=$record->amount;
$name_=$record->member->full_name ?? '-';
$telephone=$record->member->telephone ?? '-';
$membership=$record->member->category->name;

$message0="";
$transaction=$record->transaction;

if($language=="kinyarwanda")
$message0=$name_. " , kwiyandikisha byagenze neza nk'umunyamuryango wa patriots, mu kiciro cya ".$membership;

else $message0=$name_. " , you have been successfully
registered in patriots family with ".$membership." membership";

if(!Sms_outbox::where("transaction",$external_transaction_id)->exists())
sendSMS($message0, $telephone,$external_transaction_id);

Payment::where('trxid',$external_transaction_id)
    ->update(['status' => 1,'transaction' => $momo_ref_number]); 
    Log::info('updated');
    
}
 
catch (\Exception $e) {
         Log::info($e->getMessage());
}
}
}
}