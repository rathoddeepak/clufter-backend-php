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

error_reporting(E_ALL); // Error/Exception engine, always use E_ALL

ini_set('ignore_repeated_errors', TRUE); // always use TRUE

ini_set('display_errors', FALSE); // Error/Exception display, use FALSE only in production environment or real server. Use TRUE in development environment

ini_set('log_errors', TRUE); // Error/Exception file logging engine.
ini_set('error_log', 'errors.log'); // Logging file path

//JOB Status
$Assigned = 0; $Started = 1; $Successful = 2; $Failed = 3; $InProgress = 4; $Unassigned = 6; $Accepted = 7; $Decline = 8;
$Cancel = 9; $Deleted = 10;
//Job Types
$pickup = 0;
$delivery = 1;

$taskData = Tw_GetWebHookData();
parse_str($taskData, $taskData);

$job_status = $taskData['job_status'];
$vo_id = $taskData['order_id'];

//$db->insert('temp', [
  //'data' => 'Job Status -> '. $job_status 
//]);

if($taskData['job_type'] == $pickup){
  if($job_status == $Started){
    $hero = Tw_GetHeroFromFleetId($taskData['fleet_id']);
    $check = $result = $db->where('id', $vo_id)->getOne(ORDER_VENDOR);
    //if($check->hero_id != 0){
      //echo 'Hero Already Assinged';
      //return;
    //}
    $orderData = Tw_OrderData($check->order_id);
    $vendorData = Tw_VendorData($check->vendor_id);
    if(!$vendorData)return "Restaurant Not Found";
    if(!$orderData)return "Order Not Found";
    $status = $check->status == FOOD_PREPARED ? DELIVERY_F_PREPARED : DELIVERY_FN_PREPARED;
    $result = $db
    ->where('id', $vo_id)
    ->update(ORDER_VENDOR, ['hero_id' => $hero->user_id, 'status' => $status]);
    $title = "Our Hero is On Way To {$vendorData->name}";
    $content = 'Now You Can Call Now 📞 Our Delivery Hero';
    $notification = [
      'sender_type' => DLH,
      'sender_id' => $hero->user_id,
      'recipient_id' => $orderData->user_id,
      'recipient_type' => USER,
      'notify_type' => DHNUS,
      'title' => $title,
      'content' => $content,
      'content_id' => $vo_id,      
      'notifyData' => [
        'title' => $title,
        'content' => $content,
        'user_ids' => [$orderData->user_id],
        'data' => [
          'type' => DHNUS,
          'type_data' => $vo_id
        ]
      ]
    ];
    Tw_RegisterNotification($notification);    
  }else if($job_status == $Successful){
    $status = HAS_PICKED_F;
    $allowed = [HAS_PICKED_F, HAS_PICKED_C]; 
    $hero = Tw_GetHeroFromFleetId($taskData['fleet_id']);
    $check = $result = $db->where('id', $vo_id)->getOne(ORDER_VENDOR);
    $orderData = Tw_OrderData($check->order_id);  
    $vendorData = Tw_VendorData($check->vendor_id);
    if($check->status >= HAS_PICKED_F){
      return;
    }
    if(!$vendorData)return "~Restaurant Not Found";
    if(!$orderData)return "~Order Not Found";
    if(!in_array($status, $allowed))return "~Invalid Order Status";
    $title = "Picked From {$vendorData->name}";
    $content = "Our Hero Has Picked Food From {$vendorData->name}";
    $notification = [
      'sender_type' => DLH,
      'sender_id' => $hero->user_id,
      'recipient_id' => $orderData->user_id,
      'recipient_type' => USER,
      'notify_type' => DLPC,
      'title' => $title,
      'content' => $content,
      'content_id' => $check->order_id,      
      'notifyData' => [
        'title' => $title,
        'content' => $content,
        'user_ids' => [$orderData->user_id],
        'data' => [
          'type' => DLPC,
          'type_data' => $check->order_id
        ]
      ]
    ];
    Tw_RegisterNotification($notification);
    $result = $db
    ->where('id', $vo_id)  
    ->update(ORDER_VENDOR, ['hero_id' => $hero->user_id, 'status' => $status]);  
    return $result ? 'Order updated successfully' : $e;
  }else if($Cancel == $job_status || $job_status == $Decline || $job_status == $Failed){    
    $check = $result = $db->where('id', $vo_id)->getOne(ORDER_VENDOR);
    $orderData = Tw_OrderData($check->order_id);
    $db->where('id', $orderData->id)->update(ORDERS, [
      'status' => VDRFDCANCEL
    ]);
    $db->where('order_id', $orderData->id)->update(ORDER_VENDOR, [
      'status' => VDRFDCANCEL
    ]);
    $content = "Order Cancelled As Per Your Request!";
    $title = $orderData->pay_method == COD ? "😔😔 Sorry! We Will Try Improve Our Self" : "Contact Our Support For Refund!";
    $notification = [
      'sender_type' => VENDOR,
      'sender_id' => $check->vendor_id,
      'recipient_id' => $orderData->user_id,
      'recipient_type' => USER,
      'notify_type' => VNUSOS,
      'title' => $title,
      'content' => $content,      
      'content_id' => $check->order_id,      
      'notifyData' => [
        'title' => $title,
        'content' => $content,
        'user_ids' => [$orderData->user_id],     
        'data' => [
          'type' => VNUSOS,
          'type_data' => $check->order_id
        ]
      ]
    ];
    return;
  }
}else if($taskData['job_type'] == $delivery){
  if($Successful == $job_status){
    $check = $result = $db->where('id', $vo_id)->getOne(ORDER_VENDOR);
    $orderData = Tw_OrderData($check->order_id);
    $result = Tw_FinalDelivery($vo_id,$orderData->user_id,HAS_DELIVERED,$check->id);
    return;
  }else if($Cancel == $job_status || $job_status == $Decline || $job_status == $Failed){    
    $check = $result = $db->where('id', $vo_id)->getOne(ORDER_VENDOR);
    $orderData = Tw_OrderData($check->order_id);
    $db->where('id', $orderData->id)->update(ORDERS, [
      'status' => VDRFDCANCEL
    ]); 
    $db->where('order_id', $orderData->id)->update(ORDER_VENDOR, [
      'status' => VDRFDCANCEL
    ]);
    $content = "Order Cancelled As Per Your Request!";
    $title = $orderData->pay_method == COD ? "😔😔 Sorry! We Will Try Improve Our Self" : "Contact Our Support For Refund!";
    $notification = [
      'sender_type' => VENDOR,
      'sender_id' => $check->vendor_id,
      'recipient_id' => $orderData->user_id,
      'recipient_type' => USER,
      'notify_type' => VNUSOS,
      'title' => $title,
      'content' => $content,      
      'content_id' => $check->order_id,      
      'notifyData' => [
        'title' => $title,
        'content' => $content,
        'user_ids' => [$orderData->user_id],     
        'data' => [
          'type' => VNUSOS,
          'type_data' => $check->order_id
        ]
      ]
    ];
    Tw_RegisterNotification($notification);
    return;
  }
}

?>