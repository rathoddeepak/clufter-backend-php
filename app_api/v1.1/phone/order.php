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
$post = $_POST;
if(!empty($post['postData'])){    
    $post = json_decode($post['postData'], true);
}
$req = empty($post['req']) ? 'br' : $post['req'];
if($req == 'lio'){
  $vendor_id = $post['vendor_id'];  
  $records = $db
  ->where('vendor_id', $vendor_id)
  ->where('status', FOOD_NOT_PREPARED, '>')
  ->where('status', HAS_PICKED_F, '<')
  ->where('paid', 1)
  ->orderBy('time', 'desc')
  ->get(ORDER_VENDOR);
  $pending = $db
	  ->where('vendor_id', $vendor_id)
	  ->where('status', FOOD_NOT_PREPARED)
	  ->where('paid', 1)
	  ->orderBy('time', 'desc')
	  ->get(ORDER_VENDOR);
  $finalOrders = [];  
  $finalP = [];
  foreach ($records as $record){        
    $finalOrders[] = Tw_FormatInitialOrder($record);
  }
  foreach ($pending as $pnd){        
    $finalP[] = $pnd->id;
  }
  $vendorData = Tw_VendorData($vendor_id);
  $result = [
  	'odrs' => $finalOrders,
  	'pOdrs' => $finalP,
    'sts' => $vendorData->delivery
  ];
}else if($req == 'lpo'){
	$vendor_id = $post['vendor_id'];	
	$records = $db
	  ->where('vendor_id', $vendor_id)
	  ->where('status', FOOD_NOT_PREPARED)
	  ->where('paid', 1)
	  ->orderBy('time', 'desc')
	  ->get(ORDER_VENDOR);
	$finalOrders = [];  
	foreach ($records as $record)$finalOrders[] = Tw_FormatPendingOrder($record);
	$result = $finalOrders;
}else if($req == 'lou'){
	$vendor_id = $post['vendor_id'];
	$records = $db
	  ->where('vendor_id', $vendor_id)
    ->where('status', HAS_PICKED_F, '<')
	  ->where('paid', 1)
	  ->orderBy('time', 'desc')
	  ->get(ORDER_VENDOR);
	$finalOrders = [];
  $pending = $db
    ->where('vendor_id', $vendor_id)
    ->where('status', FOOD_NOT_PREPARED)
    ->where('paid', 1)
    ->orderBy('time', 'desc')
    ->get(ORDER_VENDOR);
  $finalP = [];
  foreach ($pending as $pnd){        
      $finalP[] = $pnd->id;
  }
	foreach ($records as $record)$finalOrders[] = Tw_FormatUpdateOrder($record);
	$result = [
    'odrs' => $finalOrders,
    'pOdrs' => $finalP
  ];
}else{
	$result = "~Bad Request";
}

function Tw_FormatPendingOrder($record){
    $orderData = Tw_OrderData($record->order_id);    
    $userData = Tw_UserData($orderData->user_id);
    $addressData = Tw_AddressData($orderData->address_id);
    $foodItems = Tw_GetFoodsByOrderId($record->vendor_id, $record->order_id);   
    $amount = 0;
    $items = [];    
    foreach ($foodItems as $foodItem){
    	$amount += $foodItem['price'];
    	$items[] = [
    		'n' => $foodItem['name'],
    		'q' => $foodItem['quantity'],
        'a' => $foodItem['adn']
    	];
    }
    $order = [        
  		'id' => $record->order_id,
      'vid' => $record->id,
  		'time' => Tw_TimeHumanType($record->time),
  		'total' => $amount,
  		'address' => $addressData->cl_address,
  		'name' => $userData['name'],			
      'phone' => $userData['phone_no'],
  		'items' => $items
    ];
    if(!empty($orderData->anyreq)){
      $order['req'] = $orderData->anyreq;
    }
    return $order;
}

function Tw_FormatInitialOrder($record){
    $orderData = Tw_OrderData($record->order_id);    
    $userData = Tw_UserData($orderData->user_id);
    $addressData = Tw_AddressData($orderData->address_id);
    $foodItems = Tw_GetFoodsByOrderId($record->vendor_id, $record->order_id);
    $riderValid = [DELIVERY_FN_PREPARED,DELIVERY_F_PREPARED];
    $tleft = (($record->acptm == 0 ? $record->acptm : $record->time) + ($record->ttc * 60)) - time();
    $tleft = $tleft <= 0 ? 0 : $tleft;
    $amount = 0;
    $items = [];
    $rider = null;
    if(in_array($record->status, $riderValid)){
    	$vdata = Tw_VendorData($record->vendor_id);
    	$rider = Tw_DHeroData($record->hero_id);
    	$distance = Tw_CalDistance($rider['lat'], $rider['lng'], $vdata->lat, $vdata->long);    
	    $time = round($distance * PER_KM_TIME);
	    $rider = [
	      'name' => $rider['name'],
	      'phone' => $rider['phone_no'],
	      'time' => $time
	    ];
    }
    foreach ($foodItems as $foodItem){
    	$amount += $foodItem['price'];
    	$items[] = [
    		'n' => $foodItem['name'],
    		'q' => $foodItem['quantity'],
        'a' => $foodItem['adn'],
    	];
    };
    $order = [        
  		'id' => $record->order_id,
  		'time' => Tw_TimeHumanType($record->time),
  		'total' => $amount,
  		'address' => $addressData->cl_address,
  		'name' => $userData['name'],	
      'phone' => $userData['phone_no'],
  		'status' => $record->status,      
      'late' => $record->ckt > $record->ttc,
  		'tleft' => $tleft, //Time Left For Cooking,
      'ttc' => $record->ttc * 60,
  		'items' => $items,
  		'rider' => $rider
    ];
    if(!empty($orderData->anyreq)){
      $order['req'] = $orderData->anyreq;
    }
    return $order;
}

function Tw_FormatUpdateOrder($record){    
    $riderValid = [DELIVERY_FN_PREPARED,DELIVERY_F_PREPARED];
    $tleft = (($record->acptm == 0 ? $record->acptm : $record->time) + ($record->ttc * 60)) - time();
    $tleft = $tleft <= 0 ? 0 : $tleft;
    $amount = 0;
    $items = [];
    $rider = null;  
    if(in_array($record->status, $riderValid)){
      $vdata = Tw_VendorData($record->vendor_id);
      $rider = Tw_DHeroData($record->hero_id);
      $distance = Tw_CalDistance($rider['lat'], $rider['lng'], $vdata->lat, $vdata->long);    
      $time = round($distance * PER_KM_TIME);
      $rider = [
        'name' => $rider['name'],
        'phone' => $rider['phone_no'],
        'time' => $time
      ];
    }
    $order = [        
      'id' => $record->order_id,
      'status' => $record->status,    
      'tleft' => $tleft, //Time Left For Cooking    
      'rider' => $rider
    ];
    return $order;
}

if(is_string($result) && substr($result,  0, 1) == '~')
     Tw_HeaderExit(400, substr($result,  1));
else Tw_HeaderExit(200, $result);
?>