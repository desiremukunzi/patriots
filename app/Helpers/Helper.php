<?php
use Carbon\Carbon;
use App\Church;
use App\Language;
use App\Member;
use App\Ug_member;
use App\Offering;
use App\Offering_type;
use App\Payment;
use App\RdaLocation;
use App\Sms_outbox;
use App\Ussd_flow;
use App\Setting;
use App\Category;
use App\Ug_offering;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;


function randomTimeLineBadge() {
    $classes =  [ 'kt-list-timeline__badge--success', 'kt-list-timeline__badge--danger', 'kt-list-timeline__badge--warning', 'kt-list-timeline__badge--primary', 'kt-list-timeline__badge--brand' ];
    return $classes[rand(0, 4)];
}

function active_link($name) {

    if (request()->is('country') || request()->is('province/*') || request()->is('district/*') || request()->is('church/*')  ) {
        if ($name == 'dashboard') {
            return 'kt-menu__item--here';
        }

    }

    if (request()->is('users') || request()->is('users/*')) {
        if ($name == 'users') {
            return 'kt-menu__item--here';
        }
    }

    return null;
}


function provinces() {
    return Church::distinct()->orderBy('province', 'asc')->get(['province as name']);
}

function districts($province) {
    return Church::where('province', $province)->distinct()->orderBy('district', 'asc')->get(['district as name']);
}
function church($district) {
    return Church::where('district', $district)->distinct()->orderBy('church', 'asc')->get();
}
function cells($church) {
    return Church::where('church', $church)->distinct()->orderBy('cell', 'asc')->get(['cell as name']);
}

function villages($cell) {
    return Church::where('cell', $cell)->distinct()->orderBy('village', 'asc')->get(['village as name']);
}

function count_members($start_date,$end_date,$group,$id)
{
    if($group=="province"){
    return Member::where('status','1')->whereBetween('created_at', [$start_date, $end_date])->where($group,$id)->count();}

    if($group=="district"){
    $members=Member::where('status','1')->whereBetween('created_at', [$start_date, $end_date])->where($group,$id)->count();
        return str_pad($members,3,"0",STR_PAD_LEFT).'M';

}

    if($group=="church"){
    $members=Member::where('status','1')->whereBetween('created_at', [$start_date, $end_date])->where($group,$id)->count();
        return str_pad($members,3,"0",STR_PAD_LEFT).'M';}

    if($group=="deleted_at"){
    $members=Member::where('status','1')->whereBetween('created_at', [$start_date, $end_date])->whereNotNull('province')->whereNull($group)->count();
    return $members.'M';
}
}
function countMemberAll($start_date,$end_date)
{
return Member::where('status','1')->whereBetween('created_at', [$start_date, $end_date])->count();
}
function countMemberProvince($start_date,$end_date,$province)
{
    return Member::where('status','1')->whereBetween('created_at', [$start_date, $end_date])->where('province',$province)->count();
}
function countMemberDistrict($start_date,$end_date,$province,$district)
{
    $members=Member::where('status','1')->whereBetween('created_at', [$start_date, $end_date])->where(['province'=>$province])->where('district',$district)->count();
    return str_pad($members,3,"0",STR_PAD_LEFT).'M';
}
function countMemberChurch($start_date,$end_date,$province,$district,$church)
{
    $members=Member::where('status','1')->whereBetween('created_at', [$start_date, $end_date])->where(['province'=>$province,'district'=>$district,'church'=>$church])->count();
    return str_pad($members,3,"0",STR_PAD_LEFT).'M';
}
function Count_amount($start_date,$end_date,$location,$group)
{
    $church_codes=Church::where($location,$group)->pluck('church_code');
    $amount=Payment::where('status','1')->whereBetween('created_at', [$start_date, $end_date])->whereIn('church_code',$church_codes)->sum('amount');
    if($location=="province")
        return $amount;
    if($location=="district")
        return $amount;
    if($location=="deleted_at")
        return $amount.' RWF';
    return str_pad($amount,5,"0",STR_PAD_LEFT).'F';

}
function CountAmountAll($start_date,$end_date)
{
$amount=Payment::where('status','1')->whereBetween('created_at', [$start_date, $end_date])->sum('amount');
return $amount.' RWF';
}
function CountAmountProvince($start_date,$end_date,$province)
{
$church_codes=Church::where('province',$province)->pluck('church_code');
    return $amount=Payment::where('status','1')->whereBetween('created_at', [$start_date, $end_date])->whereIn('church_code',$church_codes)->sum('amount');
}
function CountAmountDistrict($start_date,$end_date,$province,$district)
{
$church_codes=Church::where(['province'=>$province,'district'=>$district])->pluck('church_code');
    $amount=Payment::where('status','1')->whereBetween('created_at', [$start_date, $end_date])->whereIn('church_code',$church_codes)->sum('amount');
        return str_pad($amount,5,"0",STR_PAD_LEFT).'F';

}
function CountAmountChurch($start_date,$end_date,$church_code)
{
    $amount=Payment::where('status','1')->where('church_code',$church_code)->whereBetween('created_at', [$start_date, $end_date])->sum('amount');
        return str_pad($amount,5,"0",STR_PAD_LEFT).'F';

}
function countOfferings($start_date,$end_date,$offer,$location,$value)
{   if($location=='deleted_at')
    $amount=Payment::where('status','1')->where('fee_type',$offer)->whereBetween('created_at', [$start_date, $end_date])->sum('amount');
    else{
       $church_codes=Church::where($location,$value)->pluck('church_code');
       $amount=Payment::where('status','1')->whereIn('church_code',$church_codes)->where('fee_type',$offer)->whereBetween('created_at', [$start_date, $end_date])->sum('amount');
        }
    return $amount.' RWF';
}
function code()
{
        $lastKnownID = Member::withTrashed()->orderBy('code', 'desc')->limit(1)->value('code');
        return $newId=str_pad($lastKnownID+1,7,"0",STR_PAD_LEFT);

}
function UgCode()
{
        $lastKnownID = Ug_member::withTrashed()->orderBy('id', 'desc')->limit(1)->value('code');
        return $newId=str_pad($lastKnownID+1,7,"0",STR_PAD_LEFT);

}
function all_districts() {
    return Church::distinct()->orderBy('district', 'asc')->get(['district as name']);
}
function all_sectors() {
    return Church::distinct()->orderBy('church', 'asc')->get(['church as name']);
}
function all_cells() {
    return Church::distinct()->orderBy('cell', 'asc')->get(['cell as name']);
}
function all_villages() {
    return Church::distinct()->orderBy('village', 'asc')->get(['village as name']);
}
function sendSMS($message,$number,$transaction)
{
    //$source=\App\Cooperative::where('id',$cooperative_id)->value('name');
    $client = new \GuzzleHttp\Client([
        // Base URI is used with relative requests
        'base_uri' => 'http://api.rmlconnect.net/bulksms/bulksms',
        // You can set any number of default request options.
        'timeout' => 100.0,
    ]);
    $parms = [
        'username' => 'altum',
        'password' => 'VnpRlXSf',
        'type' => 0,
        'dlr' => 1,
        'destination' => "+".validSMSNumber($number),
        'source' =>'CFMS',
        'message' => $message
    ];

    $uri = "http://api.rmlconnect.net/bulksms/bulksms?" . http_build_query($parms);
    $res = $client->get($uri);
    $resp = $res->getBody()->getContents();

    $cResp = explode("|", $resp);
    if ($cResp[0] == '1701') {
        \App\Sms_outbox::create([
            'sender' => 'CFMS',
            'message' => $message,
            'telephone' =>$number,
            'transaction' =>$transaction,
         ]);
        return true;
    }
    return false;
}

function validSMSNumber($num)
{
    if (preg_match('[^\+250|250]', $num))
        return trim($num, "+");
    else {
        if ($num[0] == '0')
            return "25" . $num;
        else
            return "250" . $num;
    }


}
function member_id($session)
{
    return Member::where('session',$session)->orderBy('id','desc')->limit(1)->value('id');
}
function UgMember_id($session)
{
    return Ug_member::where('session',$session)->orderBy('id','desc')->limit(1)->value('id');
}
function LastPayment($msisdn)
{
    return \App\Payment::where('tel',$msisdn)->orderBy('id','desc')->limit(1)->value('id');
}
function memberUpdate($field, $input, $session)
{
     return Member::where('id',member_id($session))->update([$field=>$input]);
}
function updateMember($field, $input, $code)
{
     return Member::where('code',$code)->update([$field=>$input]);
}
function updateMembre($where,$value,$field, $input)
{
     return Member::where($where,$value)->update([$field=>$input]);
}
function UgMemberUpdate($field, $input, $session)
{
     return Ug_member::where('id',UgMember_id($session))->update([$field=>$input]);
}
function memberEdit($field, $input, $id)
{
     return Member::where('telephone',$id)->orWhere('code','=',$id)->update([$field=>$input]);
}
function PaymentUpdate($field,$input,$msisdn)
{
     return \App\Payment::where('id',LastPayment($msisdn))->update([$field=>$input]);
}
function saveFlow($language, $message, $input, $session, $msisdn, $level, $sublevel1)
{
    $MotoPay=new Ussd_flow();$MotoPay->language=$language;$MotoPay->message=$message;$MotoPay->input=$input;
    $MotoPay->session=$session;$MotoPay->telephone=$msisdn;$MotoPay->level=$level;
    $MotoPay->sublevel1=$sublevel1;$MotoPay->save();

    return true;
}

function DistrictChurch($upperlevel, $level, string $session)
{
       $variables=Church::select($level)->orderBy($level,'asc')->where($upperlevel,Member::
       where('id',member_id($session))->value($upperlevel))->distinct()->get();
        $count=1;
        $data2=array();
        foreach ($variables as $key => $variable)
        {
            $data2[]="#".$count.".".$variable->$level;
            $count++;
        }
        return implode($data2);
}
function ChurchList(string $session)
{
    $provDist=Member::where('id',member_id($session))->first();

    $variables=Church::select('church')->orderBy('church','asc')
        ->where(['province'=>$provDist->province,'district'=>$provDist->district])->distinct()->get();
        $count=1;
        $data2=array();
        foreach ($variables as $key => $variable)
        {
            $data2[]="#".$count.".".$variable->church;
            $count++;
        }
        return implode($data2);
}
function EditChurchList(string $session)
{
   $provDist=Ussd_flow::where(['session'=>$session,'message'=>'editChurchAddress'])->first();
    $variables=Church::select('church')
        ->where(['province'=>$provDist->level,'district'=>$provDist->sublevel1])
        ->orderBy('church','asc')
        ->distinct()->get();

        $count=1;
        $data2=array();
        foreach ($variables as $key => $variable)
        {
            $data2[]="#".$count.".".$variable->church;
            $count++;
        }
        return implode($data2);
}
function EditDistrictList($session)
{
    $variables=Church::select('district')->orderBy('district','asc')
        ->where('province',Ussd_flow::where(['session'=>$session,'message'=>'editChurchAddress'])->latest()
            ->value('level'))->distinct()->get();
        $count=1;
        $data2=array();
        foreach ($variables as $key => $variable)
        {
            $data2[]="#".$count.".".$variable->district;
            $count++;
        }
        return implode($data2);
}

function UpdateProvince($level,$input,$session)
{
    $locations=Church::select($level)->orderBy($level,'asc')->distinct()->get();
        $count=1;
        foreach ($locations as $key => $location)
        {
           if($input==$count)
           {
            $discovered=$location->$level;
            memberUpdate($level,$discovered,$session);
           }

            $count++;
        }
        return $discovered;

}
function UpdateDistrictChurch($upperlevel,$level,$input,$session)
{
    $locations=Church::select($level)->orderBy($level,'asc')->where($upperlevel,Member::where('id',member_id($session))->value($upperlevel))->distinct()->get();
        $count=1;
        foreach ($locations as $key => $location)
        {
           if($input==$count)
           {
            $discovered=$location->$level;
            memberUpdate($level,$discovered,$session);
           }

            $count++;
        }
        return $discovered;

}
function UpdateChurch($input,$session)
{
    $provDist=Member::where('id',member_id($session))->first();

    $locations=Church::select('church')->orderBy('church','asc')
        ->where(['province'=>$provDist->province,'district'=>$provDist->district])->distinct()->get();

        $count=1;
        foreach ($locations as $key => $location)
        {
           if($input==$count)
           {
            $discovered=$location->church;
            memberUpdate('church',$discovered,$session);
           }

            $count++;
        }
        return $discovered;

}
function EditProvince($input,$session)
{
    $locations=Church::select('province')->orderBy('province','asc')->distinct()->get();
        $count=1;
        foreach ($locations as $key => $location)
        {
           if($input==$count)
           {
            $discovered=$location->province;
             saveFlow('english','editChurchAddress', $input, $session, "250788354222", $discovered, 'district');;

           }

            $count++;
        }
        return $discovered;

}
function EditDistrict($input,$session)
{
    $locations=Church::select('district')->orderBy('district','asc')
        ->where('province',Ussd_flow::where(['session'=>$session,'message'=>'editChurchAddress'])
            ->value('level'))->distinct()->get();
        $count=1;
        foreach ($locations as $key => $location)
        {
           if($input==$count)
           {
            $discovered=$location->district;
            Ussd_flow::where(['session'=>$session,'message'=>'editChurchAddress'])->update(['sublevel1'=>$discovered]);
           }

            $count++;
        }
        return $discovered;

}
function EditChurch($input,$session,$code)
{
    $provDist=Ussd_flow::where(['session'=>$session,'message'=>'editChurchAddress'])->latest()->first();
    $locations=Church::select('church')
        ->where(['province'=>$provDist->level,'district'=>$provDist->sublevel1])
        ->orderBy('church','asc')
        ->distinct()->get();
        $count=1;
        foreach ($locations as $key => $location)
        {
           if($input==$count)
           {
            $discovered=$location->church;
            updateMember('church',$discovered,$code);
            updateMember('province',$provDist->level,$code);
            updateMember('district',$provDist->sublevel1,$code);

            $church_code2 = Church::where(["province" => $provDist->level, "district" => $provDist->sublevel1,"church" => $discovered])->value('church_code');
            updateMember('church_code',$church_code2,$code);
           }
            $count++;
        }
        return $discovered;

}

function OthersRwanda($upperlevel, $level, string $session)
{
       $variables=RdaLocation::select($level)->orderBy($level,'asc')->where($upperlevel,Member::where('id',member_id($session))->value($upperlevel))->distinct()->get();
        $count=1;
        $data2=array();
        foreach ($variables as $key => $variable)
        {
            $data2[]="#".$count.".".$variable->$level;
            $count++;
        }
        return implode($data2);
}
function UpdateProvinceRda($level,$input,$session)
{
    $locations=RdaLocation::select($level)->orderBy($level,'asc')->distinct()->get();
        $count=1;
        foreach ($locations as $key => $location)
        {
           if($input==$count)
           {
            $discovered=$location->$level;
            memberUpdate($level,$discovered,$session);
           }

            $count++;
        }
        return $discovered;

}
function UpdateOthersRda($upperlevel,$level,$input,$session)
{
    $locations=RdaLocation::select($level)->orderBy($level,'asc')->where($upperlevel,Member::where('id',member_id($session))->value($upperlevel))->distinct()->get();
        $count=1;
        foreach ($locations as $key => $location)
        {
           if($input==$count)
           {
            $discovered=$location->$level;
            memberUpdate($level,$discovered,$session);
           }

            $count++;
        }
        return $discovered;

}
function UnderMaintenance()
{
    return array(
    "action" =>"FC" ,
    "message"=>"Under Maintenance##0.Back");
}
function InvalidInput($language)
{
     return array(
     "action" =>"FC" ,
     "message"=>Dict($language,'invalidInput'));
}
function NotFound($language)
{
     return array(
     "action" =>"FC" ,
     "message"=>Dict($language,'notFound'));
}
function No($language)
{
     return array(
     "action" =>"FC" ,
     "message"=>Dict($language,'no'));
}
function FC($message)
{
     return array(
     "action" =>"FC" ,
     "message"=>$message);
}
function FB($message)
{
     return array(
     "action" =>"FB" ,
     "message"=>$message);
}
function Pay2($input,$id,$telephone)
{
$uri = 'http://api.ishema.rw/api/v1/debit';

        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $uri,
            // You can set any number of default request options.
            'timeout' => 120.0,
        ]);

        try {
            // make the request
            $response = $client->request('POST', $uri, [
                    'form_params' => [
                    'token' => 'KNxnrqgFxmYzC64XkEjdnX6yV5Gox4',
                    'amount' => $input,
                    'msisdn' => $telephone,
                    'external_id' => $id,
                    'postback_url'=>'http://cfms.mopay.rw/api/paymentResponse',
                    'client_name'=>"Desire"
                ]
            ]);
        } catch (RequestException $e) {
            return response()
                ->json(['error' => 'Payment Sever Error!'], 401);
        } catch (GuzzleException $e) {
            return response()
                ->json(['error' => 'Payment Sever Error!'], 401);
        }

        $body = (string) $response->getBody();
        $status = json_decode($response->getBody())->status;
        $message = json_decode($response->getBody())->message;

        if ($status != 1) {
            return response()
                ->json(['status' => 2, 'message'  => $message]);
        }

        return response()
            ->json(['status' => 0, 'message'  => $message]);
    }
    function Pay0($input,$code,$msisdn)
    {
        $number=Setting::where('name','MomoNumber')->value('value');

        $uri = 'https://pay.mopay.rw/api/v1/payment';
        $client = new Client([
            'base_uri' => $uri,
            'timeout' => 120.0,
        ]);

        try {
            $response = $client->request('POST', $uri,

                ['headers'=> ['Accept' => 'application/json'],
                'form_params' => [
                    'amount' => $input,
                    //'amount' => 5,
                    'motari_telephone' => $number,
                    //'motari_telephone' => "250782147911",
                    //'motari_telephone' => "250788354222",
                    'motari_code' => $code,
                    'motari_name' => "SDA",
                    'client_telephone' => $msisdn,
                    'platform' => 0,
                    'source' => "CFMS",
                    ]
            ]);
        } catch (RequestException $e) {
            return response()
                ->json(['error' => 'Payment Sever Error!'], 401);
        } catch (GuzzleException $e) {
            return response()
                ->json(['error' => 'Payment Sever Error!'], 401);
        }
        $transaction = json_decode($response->getBody())->transaction;

        $session=Payment::where('tel',$msisdn)->latest()->take(1)->value('session');
        Payment::where('session',$session)->whereNotNull('amount')->update(['transaction'=>$transaction]);

        return $body = (string) $response->getBody();

       // return $response;
    }
    function Dict($language,$hint){
    return Language::where('hint',$hint)->value($language);
    }
    function PaymentSession()
    {
        $session = Payment::orderBy('session', 'desc')->limit(1)->value('session');
        return $session+1;
    }
    function FeeAndAmount($msisdn){
     $session=Payment::where('tel',$msisdn)->orderBy('session','desc')
                        ->limit(1)->value('session');
     $variables = Payment::where(['session'=>$session,'tel'=>$msisdn])->get();

        $total=0;
        $data2=array();
        foreach ($variables as $key => $variable)
        {
            $total=$total+$variable->amount;
            $description=$variable->fee_description;
            if($description)
              $data2[]="#".$variable->fee_name."(".$description."): ".$variable->amount;
            else $data2[]="#".$variable->fee_name.": ".$variable->amount;
        }
        return implode($data2);
    }
    function feeAmount($session){     
    $variables = Payment::where(['session'=>$session])->get();

        $total=0;
        $data2=array();
        foreach ($variables as $key => $variable)
        {
            $total=$total+$variable->amount;
            $description=$variable->fee_description;
            if($description)
              $data2[]=$variable->fee_name."(".$description."): ".$variable->amount.",";
            else $data2[]=$variable->fee_name.": ".$variable->amount.", ";
        }
        return implode($data2);
    }
    function feeTypeAmount($session){     
    $variables = Payment::where(['session'=>$session])->get();

        $total=0;
        $data2=array();
        foreach ($variables as $key => $variable)
        {
            $total=$total+$variable->amount;
            $description=$variable->fee_description;
            if($description)
              $data2[]=$description.": ".$variable->amount.",";
            else $data2[]=$variable->fee_name.": ".$variable->amount.", ";
        }
        return implode($data2);
    }
    function TotalAmount($msisdn){

     $session=Payment::where('tel',$msisdn)->orderBy('session','desc')
                        ->limit(1)->value('session');
     return Payment::where(['session'=>$session,'tel'=>$msisdn])->sum('amount');
    }
    function Sms(){
     return $sms=Sms_outbox::count();
    }
    function Offerings($language)
    {
    $variables=Offering::orderBy('id','asc')->get();
        $count=1;
        $data2=array();
        foreach ($variables as $key => $variable)
        {
            $data2[]="#".$count.".".$variable->$language;
            $count++;
        }
        return implode($data2);
    }
    function Offering_types($language)
    {
    $variables=Offering_type::orderBy('id','asc')->get();
        $count=1;
        $data2=array();
        foreach ($variables as $key => $variable)
        {
            $data2[]="#".$count.".".$variable->$language;
            $count++;
        }
        return implode($data2);
    }
    function UgOfferings($language,$id)
    {
    $variables=Ug_offering::where('offering_type_id',$id)->orderBy('id','asc')->get();
        $count=1;
        $data2=array();
        foreach ($variables as $key => $variable)
        {
            $data2[]="#".$count.".".$variable->$language;
            $count++;
        }
        return implode($data2);
    }
    function UpdateOffering($language,$input,$msisdn){
    $offerings=Offering::orderBy('id','asc')->get();
        $count=1;
        $discovered="empty";
        foreach ($offerings as $key => $offering)
        {
           if($input==$count)
           {
            $discovered=$offering->english;
            PaymentUpdate('fee_type',$discovered,$msisdn);
            PaymentUpdate('fee_name',$offering->$language,$msisdn);
           }

            $count++;
        }
        return $discovered;

}
function UpdateUgOffering($language,$input,$msisdn,$id){
        $offerings=Ug_Offering::where('offering_type_id',$id)->orderBy('id','asc')->get();
        $count=1;
        $discovered="empty";
        foreach ($offerings as $key => $offering)
        {
           if($input==$count)
           {
            $discovered=$offering->english;
            PaymentUpdate('fee_type',$discovered,$msisdn);
            PaymentUpdate('fee_name',$offering->$language,$msisdn);
           }

            $count++;
        }
        return $discovered;

}
function ChurchListPay(string $msisdn)
{
    $provDist=Payment::where('id',LastPayment($msisdn))->first();

    $variables=Church::select('church')->orderBy('church','asc')
        ->where(['province'=>$provDist->province,'district'=>$provDist->district])->distinct()->get();
        $count=1;
        $data2=array();
        foreach ($variables as $key => $variable)
        {
            $data2[]="#".$count.".".$variable->church;
            $count++;
        }
        return implode($data2);
}
function UpdateProvincePay($level,$input,$msisdn)
{
    $locations=Church::select($level)->orderBy($level,'asc')->distinct()->get();
        $count=1;
        foreach ($locations as $key => $location)
        {
           if($input==$count)
           {
            $discovered=$location->$level;
            PaymentUpdate($level,$discovered,$msisdn);
           }

            $count++;
        }
        return $discovered;

}
function UpdateDistrictChurchPay($upperlevel,$level,$input,$msisdn)
{
    $locations=Church::select($level)->orderBy($level,'asc')->where($upperlevel,Payment::where('id',LastPayment($msisdn))->value($upperlevel))->distinct()->get();
        $count=1;
        foreach ($locations as $key => $location)
        {
           if($input==$count)
           {
            $discovered=$location->$level;
            PaymentUpdate($level,$discovered,$msisdn);
           }

            $count++;
        }
        return $discovered;

}
function UpdateChurchPay($input,$msisdn)
{
    $provDist=Payment::where('id',LastPayment($msisdn))->first();

    $locations=Church::select('church')->orderBy('church','asc')
        ->where(['province'=>$provDist->province,'district'=>$provDist->district])->distinct()->get();

        $count=1;
        foreach ($locations as $key => $location)
        {
           if($input==$count)
           {
            $discovered=$location->church;
            PaymentUpdate('church',$discovered,$msisdn);
           }

            $count++;
        }
        return $discovered;

}
function DistrictChurchPay($upperlevel, $level, string $msisdn)
{
       $variables=Church::select($level)->orderBy($level,'asc')->where($upperlevel,Payment::
       where('id',LastPayment($msisdn))->value($upperlevel))->distinct()->get();
        $count=1;
        $data2=array();
        foreach ($variables as $key => $variable)
        {
            $data2[]="#".$count.".".$variable->$level;
            $count++;
        }
        return implode($data2);
}
function FinishUpdate($language,$code){
    $member=Member::where('code',$code)->first();
        $church_code=Church::where(["province"=>$member->province,"district"=>$member->district,
                "church"=>$member->church])->value('church_code');

                $message = Dict($language,'updated')."#". Dict($language, 'names') . " " . $member->name . "#" .
                    "Tel " . $member->telephone . "#" .
                    Dict($language, 'church') . " " .$member->province.",".$member->district.",".$member->church;
                
                return array("action" => "FB", "message" => $message);
}
function SinceStartTransactions($location,$value){
   
   if($location=='deleted_at')
   $transactions=Payment::where('status','1')->distinct('session')->count();
   else{
       $church_codes=Church::where($location,$value)->pluck('church_code'); 
       $transactions=Payment::where('status','1')->whereIn('church_code',$church_codes)->distinct('session')->count();
   }
        return str_pad($transactions,6,"0",STR_PAD_LEFT);


}
function SinceStartAmount($location,$value){
   if($location=='deleted_at')
   return Payment::where('status','1')->sum('amount');   
   else{
        $church_codes=Church::where($location,$value)->pluck('church_code');
           return Payment::where('status','1')->whereIn('church_code',$church_codes)->sum('amount');
   }
}
function SinceStartMembers(){
   $members=Member::where('status','1')->count();
          return str_pad($members,6,"0",STR_PAD_LEFT);
}
function SinceStartMembersProvince($province){
   $members=Member::where(['status'=>'1','province'=>$province])->count();
          return str_pad($members,5,"0",STR_PAD_LEFT);
}
function SinceStartMembersDistrict($province,$district){
   $members=Member::where(['status'=>'1','province'=>$province,'district'=>$district])->count();
          return str_pad($members,4,"0",STR_PAD_LEFT);
}
function SinceStartMembersChurch($province,$district,$church){  
   $members=Member::where(['status'=>'1','province'=>$province,'district'=>$district,'church'=>$church])->count();
          return str_pad($members,4,"0",STR_PAD_LEFT);
}
function creditBpr($fee_type,$amount,$church_account,$field_account,$description,$payment_id){

$client = new Client();
try {
if(($fee_type=="Tithe")||($fee_type=="Harvesting")||($fee_type=="Camp Meeting")){
$field=$amount;
$response2=$client->post('https://bpr.mopay.rw/api/v1/transaction',[
    RequestOptions::JSON=>[
        'transactionId' => 'CFMS'.$payment_id.'R1',
        'postings' =>  [
            
            [ 
                'account'=> $field_account,
                'amount'=> $field,
                'narrative' => $fee_type.$description,
            ]
            
        ]
    ]
]);
}
if($fee_type=="Offering"){
$church=round($amount*0.5);$field=$amount-$church;

  $response2=$client->post('https://bpr.mopay.rw/api/v1/transaction',[
    RequestOptions::JSON=>[
        'transactionId' => 'CFMS'.$payment_id.'R1',
        'postings' =>  [
            [ 
                'account'=> $church_account,
                'amount'=> $church,
                'narrative' => $fee_type.$description,

            ],
            
            [ 
                'account'=> $field_account,
                'amount'=> $field,
                'narrative' => $fee_type.$description
            ]            
            
        ]
    ]

]);
}

if($fee_type=="Others"){
$church=$amount;
$response2=$client->post('https://bpr.mopay.rw/api/v1/transaction',[
    RequestOptions::JSON=>[
        'transactionId' => 'CFMS'.$payment_id.'R1',
        'postings' =>  [            
            [ 
                'account'=> $church_account,
                'amount'=> $church,
                'narrative' => $fee_type.$description
            ]
            
        ]
    ]
]);
}
    } catch (\Exception $e) {
        echo $e->getMessage();
    }

    return json_decode($response2->getBody());


}
function Pay($input,$msisdn)
{
        $paym=Payment::with('member')->where('telephone',$msisdn)->latest()->take(1)->first();
        $name=$paym->member->last_name;
        $external_transaction_id='PATRI'.$paym->id;
        $number=Setting::where('name','MomoNumber')->value('value');
        $MopayNumber=Setting::where('name','MopayNumber')->value('value');
        
        $fee=round($input*3/100);$remaining=$input-$fee;
        
        $message=$name.' paid '.$paym->member->category->name.' membership in patriots';

        $client = new Client();
        $response=$client->post('https://mo.mopay.rw/api/v2/payment',[
         RequestOptions::JSON=>[
        'token' => 'bd3ZaBu9qlA3ZdxM6PEfkNot7UoO82hE',        
        'external_transaction_id' => $external_transaction_id,
        'callback_url' => 'https://patriots.mopay.rw/api/patriotsMomo',
        'debit' =>[
        'phone_number' => $msisdn,
        'amount' => $input,
        //'amount' => 300,
        'message' => $message
        ],
        'transfers' =>  [            
            [
                'phone_number'=>$msisdn,
                'amount'=> $fee,
                //'amount'=> 150,
                'message' =>  $message

            ],
            [
                //'phone_number'=> '250788354222',
                'phone_number'=> $msisdn,
                'amount'=> $remaining,
                //'amount'=> 150,
                'message' =>  $message
            ]            
        ],
        ]
    
]);
         $body = (string) $response->getBody();
         $status_code = json_decode($response->getBody())->status_code;
         if($status_code=='202'){
         $momo_ref_number = json_decode($response->getBody())->momo_ref_number;
         Payment::where('id',$paym->id)->whereNotNull('amount')->update(['trxid'=>$external_transaction_id,'transaction'=>$momo_ref_number]);
         }
         $message=json_decode($response->getBody())->message;
         return array([
                    'status'  => $status_code,
                    'message' => $message,
                ], $status_code);
    }

function countBprFailed(){
$query1=Payment::where('status','1')->whereDate('created_at', '>=','2020-09-01')->whereNull('bprRefNo')->count();

$query2=Payment::where('status','1')->whereDate('created_at', '>=','2020-09-01')->where('bprRefNo','')->count();

// $query1=Bpr_response::with('payment')->where('statusCode','!=',200)->count();
// $query2=Bpr_response::whereHas('payment',function($q){
//     $q->whereNull('bprRefNo');
// })->where('statusCode','=',200)->count();

// $query2=Payment::where('status','1')->whereDate('created_at', '>=','2020-09-01')->where('bprRefNo','')->count();

return $query1+$query2;
}
function amountBprFailed(){

$query1=Payment::where('status','1')->whereDate('created_at', '>=','2020-09-01')->whereNull('bprRefNo')->sum('amount');

$query2=Payment::where('status','1')->whereDate('created_at', '>=','2020-09-01')->where('bprRefNo','')->sum('amount');

return $query1+$query2;
}
function phoneCode($language,$input,$session,$msisdn){
    $message = Dict($language, 'phoneCode');
    saveFlow($language, $message, $input, $session, $msisdn, "1.1", 1);
    return array("action" => "FC", "message" => $message);
}
function church_code($input){
$member = Member::where('telephone', $input)->orWhere('code',$input)->first();
$church_code1 = $member->church_code;
$church_code2 = Church::where(["province" => $member->province, "district" => $member->district,
        "church" => $member->church])->value('church_code');
return $church_code1??$church_code2;
}
function bpr_number($code){
    return Church::where('church_code',church_code($code))->value('bpr_number');
}
function offering(){
return Offering::all();
}
// function topChurches($start_date,$end_date,$location,$value){
//     $searchDay = 'Sunday';
//     $searchDay2 = 'Saturday';
//     $searchDate= new Carbon();
//     $lastSunday= Carbon::createFromTimeStamp(strtotime('last $searchDay',$searchDate->timestamp));
//     $lastSaturday= Carbon::createFromTimeStamp(strtotime('last $searchDay2',$searchDate->timestamp));
//     $query=Cache::remember('topChurches',10,function (){
//         return Church::select('church','province','district','church_code',\DB::raw('(SELECT SUM(amount) FROM payments WHERE created_at between 2020-11-01  and  2020-11-21 and status=1 and churches.church_code = payments.church_code) as total'))
//             ->orderBy('total', 'DESC');});

    
//     if($location=='deleted_at')      
//       return $query->take(10)->get();
//       return $query->where($location,$value)->take(5)->get();
// }
// function topMembers($start_date,$end_date,$location,$value){
//     $query=Member::select('name','code',\DB::raw('(SELECT SUM(amount) FROM payments WHERE created_at between "'.$start_date.'" and "'.$end_date.'" and status=1 and  members.code = payments.code) as total'))->where('status','1')
//             ->orderBy('total', 'DESC');

//     if($location=='deleted_at')   
//       return $query->take(10)->get();
//       return $query->where($location,$value)->take(5)->get();
// }
function codeEdit($msisdn){
return Ussd_flow::where(['telephone' => $msisdn, 'level' => "editMember"])->latest()->value('input');
}
function payment($msisdn,$code,$church_code,$payment_session){
    $pay = new Payment();
    $pay->tel = $msisdn;
    $pay->code = $code;
    $pay->church_code = $church_code;
    $pay->session = $payment_session;
    $pay->save();
}








