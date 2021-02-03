<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Ug_ussd_flow;
use App\Ug_member;
use App\Payment;
use App\Church;
use App\Setting;
use App\MemberAttendance;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Log;



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
        
        $tel = substr($msisdn, 2, 10);
        $client = new Client();      

        if($newRequest=='1')
        {
    return $ussd=FB("kode y'umucuruzi siyo cg amafaranga ari munsi ya 100");

//         $session = Ug_ussd_flow::orderBy('session', 'desc')->limit(1)->value('session');
//         $session = $session + 1;

//         if (strpos((string)$input, "*9*") !== false) {

//         $arr = explode('*', $input);
//         if (isset($arr[2]) && isset($arr[3])) {
//         $merchant_id = (int)$arr[2];
//         $amount = (int)$arr[3];
//         //$response="";

//         Ug_ussd_flow::create(['telephone'=>$msisdn,'merchant_id'=>$merchant_id,'amount'=>$amount,'level'=>'pay','session'=>$session]);


// $URI = 'https://merchant.mopay.rw/api/v1/ussd/payment/verify';
// $data   = [
//             'merchant_id' => $merchant_id, 'telephone' => $tel, 'amount' => $amount
//           ];
// try { $response = $client->post( $URI, [
//         'headers' => ['Content-Type' => 'application/json', 'Authorization' => 'cuAfwMB9TDliIOicM7pFKg6Cs06sdJTpcxmVGG2wWtCfz7MRHtjO','Accept' => 'application/json'],
//         'body' => json_encode($data)
//     ]);               
//     } 
// catch (\Exception $e) {
//         //echo $e->getMessage();
// } 


// // $message0=$message1=$status="";

// //return $status = json_decode($response->getBody())->status;

// // if($amount<100) $message0="amafaranga ari munsi ya 100# ";
// // if($status!=200) $message1="Umucuruzi ntabonetse# ";
// $resp=$response ?? "";
// //if(($amount<100)||($status!=200))
// if($resp=="")
//  {  //$message=$message0.$message1;
//     $ussd=FB("kode y'umucuruzi siyo cg amafaranga ari munsi ya 100");
//  }
//  else
//  { $merchant = json_decode($response->getBody())->data->merchant;
//    $message="Ugiye kwishyura#Umucuruzi : ".$merchant.'#Amafaranga : '.$amount."#Bonus yawe ni 5%##1.Kwemeza";
//    $ussd=FC($message);  
//  }


// }
// }
// }
// else if($newRequest=='0')
// {
// $query = Ug_ussd_flow::orderBy('id', 'desc')->where(['telephone'=>$msisdn])->limit(1)->first();
// $level = $query->level;
// $amount=$query->amount;
// $merchant_id=$query->merchant_id;

// if(($level=='pay')&&($input=='1'))
//   {

// $URI = 'https://merchant.mopay.rw/api/v1/ussd/payment';
// $data   = [
//             'merchant_id' => (int)$merchant_id, 'telephone' => $tel, 'amount' => (int)$amount
//           ];
// $response = $client->post( $URI, [
//         'headers' => ['Content-Type' => 'application/json', 'Authorization' => 'cuAfwMB9TDliIOicM7pFKg6Cs06sdJTpcxmVGG2wWtCfz7MRHtjO','Accept' => 'application/json'],
//         'body' => json_encode($data)
//     ]); 
//         //Log::info("merchant ".(string) $response->getBody());


// $ussd=FB('Tegereza gato, nibidakunda ukande *182*7 urwego ushyiremo PIN yawe usoze Kwishyura');
//    }
// }

// return $ussd;
}
}
}
