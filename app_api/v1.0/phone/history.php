<?php
/*->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>   
    |-----------------------------------------------------------|
    |@author        Deepak Balasaheb Rathod                     |
    |@author_url    https://foodbazzar.dp/about-us/             |
    |@author_mail   rathoddeepak537@gmail.com                   |
    |@site_url      https://foodbazzar.store                    |
    |@site_mail     foodbazzar@store.com                        |
    |-----------------------------------------------------------|
    | Founder - Kshitij Kendre                                  |
    | Founder - Suhas Karande                                   |
    |-----------------------------------------------------------|
    | FoodBazzar,Mordern Food Ordering and Table Booking System |
    | FoodBazzar 2020 Copyright all rights reserved.            |
    |-----------------------------------------------------------|     
->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

if (empty($_GET['type']) 
|| !isset($_GET['type']))Tw_HeaderExit(400, "Bad Request!");
$details = $_POST;
$result = "~Bad Request";
$req = empty($details['req']) ? 'dlv' : $details['req'];
if($req == 'dlv'){
    $history = Tw_GetVOrderHistory($details);
    if(empty($details['offset'])){
        $data = Tw_CalVOrderHistory($details);        
        $result = [
            'earning' => $data['total'],
            'cancel' => $data['cancel'],
            'accept' => $data['accept'],
            'history' => $history
        ];
    }else{
        $result = ['history' => $history];
    }
}else if($req == 'odrs'){
    $history = Tw_GetVisits($details);
    if(empty($details['offset'])){
        $data = Tw_CalVisitHistory($details);        
        $result = [
            'earning' => $data['total'],
            'qr' => $data['qr'],
            'tax' => $data['tax'],
            'captain' => $data['captain'],
            'history' => $history
        ];
    }else{
        $result = ['history' => $history];
    }
}else if($req == 'bkg'){
    $history = Tw_LoadTBooking($details, $details['limit'], $details['offset']);
    if(empty($details['offset'])){
        $data = Tw_CalBkgHistory($details);        
        $result = [
            'ac' => $data['ac'],
            'cn' => $data['cn'],
            'tx' => $data['tx'],
            'tt' => $data['tt'],
            'hs' => $history
        ];
    }else{
        $result = ['hs' => $history];
    }    
}else{
    $result = "~Bad Request";
}

if(is_string($result) && substr($result,  0, 1) == '~')
     Tw_HeaderExit(400, substr($result,  1));
else Tw_HeaderExit(200, $result);

?>