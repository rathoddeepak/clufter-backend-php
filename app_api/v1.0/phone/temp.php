<?php

$req = empty($_POST['req']) ? 'orders' : $_POST['req'];
if ($req == 'orders') {
    $time = time();
    $start = strtotime(date('Y-m-d', $time).'00:00:00');
    $end = strtotime(date('Y-m-d', $time).'23:59:59');
    $orders = $db->orderBy("id","desc")->where('time',  Array($start, $end), "BETWEEN")->get(ORDERS);
    $response = [];
    foreach ($orders as $order) {
        $vendor = Tw_GetVendorsOfOrder($order->id);
        $userData = Tw_UserData($order->user_id);
        if(count($vendor) > 0){         
            $vendor = [
                'name' => $vendor[0]->name,
                'id' => $vendor[0]->id,
            ];
        }else{
            $vendor = [
                'name' => 'No Hotel Error!',
                'id' => 0
            ];
        }
        $customer = [
            'id' => $userData['id'],
            'name' => $userData['name'],
            'phone_no' => $userData['phone_no'],
        ];
        $response[] = [
            'vendor' => $vendor,
            'customer' => $customer,
            'time' => Tw_TimeHumanType($order->time),
            'id' => $order->id,
            'paid_s' => $order->paid == 1 ? 'Yes Paid' : 'Order Not Completed',
            'total' => $order->amount + $order->d_praise + DELIVERY_FEE
        ];      
    }
    Tw_HeaderExit(200, $response);
}else if ($req == 'notify') {  
    $users = $db->get(USERS);
    $user_ids = [];
    foreach($users as $user){
      $user_ids[] = $user->id;
    }        
    $notification = [
        'sender_type' => APP,
        'sender_id' => 0,
        'recipient_id' => $user_ids,
        'recipient_type' => USER,
        'notify_type' => CLNT,
        'title' => $_POST['title'],
        'content' => $_POST['content'],        
        'content_id' => 0,      
        'notifyData' => [
          'title' => $_POST['title'],
          'content' => $_POST['content'],
          'user_ids' => $user_ids,        
          'data' => [
            'type' => CLNT,
            'type_data' => 0
          ]
        ]
    ];
    Tw_RegisterNotification($notification);   
    Tw_HeaderExit(200, ['notified' => true]);
}

?>