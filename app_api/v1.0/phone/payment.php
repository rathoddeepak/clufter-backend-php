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

$req = empty($details['req']) ? 'crt_odr' : $details['req'];

if(!$req)w_HeaderExit(200, $result);

$result = '~'.$result;

if ($req == 'crt_odr') {
    $f_ids = json_decode($details['f_ids'], true);
	$orderData = [
		'amount' => $details['amount'],
        'd_praise' => $details['d_praise'],
        'user_id' => $details['user_id'],
        'food_ids' => $f_ids,
        'time' => $details['time'],
        'address_id' => $details['address_id'],        
        'pay_method' => $details['pay_method']
	];
    if(!empty($details['anyreq'])){
        $orderData['anyreq'] = $details['anyreq'];
    }
    if(!empty($details['altnum'])){
        $orderData['altnum'] = $details['altnum'];
    }
    if(!empty($details['surge_amt'])){
        $orderData['surge_amt'] = $details['surge_amt'];
    }
    $result = Tw_PlaceOrder($orderData);
}else if($req == 'pysu'){
    $result = Tw_UBKGPaymentStatus($details);
}else if($req == 'opsu'){
    $result = Tw_UODRPaymentStatus($details);
}

if(is_string($result) && substr($result,  0, 1) == '~')
     Tw_HeaderExit(400, substr($result,  1));
else Tw_HeaderExit(200, $result);

?>